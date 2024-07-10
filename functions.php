<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Iniciar sessões no WordPress
function start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'start_session', 1);

require_once get_stylesheet_directory() . '/class-theme-setup.php';
require_once get_stylesheet_directory() . '/class-jetengine-options.php';
require_once get_stylesheet_directory() . '/class-numerology-calculator.php';

new Theme_Setup();

function return_initial_audios_and_destiny_number($destiny_number) {
    $jet_engine_options = new JetEngine_Options();
    $introductions = $jet_engine_options->get_introductions();
    $all_repeaters = $jet_engine_options->get_all_repeaters();

    $result = [];

    // Áudios de introdução
    $result[] = $introductions;

    // Áudio do número de destino
    if (isset($all_repeaters['_numeros_destino_516'][$destiny_number])) {
        $result[] = $all_repeaters['_numeros_destino_516'][$destiny_number];
    } else {
        $result[] = [
            '_audio_do_numero' => 'Nenhum áudio encontrado para o número de destino.',
            '_legenda_do_audio' => '',
        ];
    }

    return $result;
}


// Hook para processar o envio dos formulários
add_action('elementor_pro/forms/new_record', function ($record, $handler) {
    // Verifique qual formulário foi enviado
    $form_name = $record->get_form_settings('form_name');

    // Obtenha os dados do formulário
    $raw_fields = $record->get('fields');
    $fields = [];
    foreach ($raw_fields as $id => $field) {
        $fields[$id] = $field['value'];
    }

    // Instancia a classe de cálculo
    require_once get_stylesheet_directory() . '/class-numerology-calculator.php';
    $calculator = new NumerologyCalculator();

    // Armazena os dados do formulário usando transients para acesso global
    switch ($form_name) {
        case 'Form1':
            // Realiza o cálculo do número de destino
            $fields['destiny_number'] = $calculator->calculateDestinyNumber($fields['birth_date']);
            set_transient('form1_submission_data', $fields, 60 * 60); // Armazena por 1 hora
            break;
        case 'Form2':
            // Realiza o cálculo do número de expressão
            $fields['expression_number'] = $calculator->calculateExpressionNumber($fields['full_name']);
            set_transient('form2_submission_data', $fields, 60 * 60); // Armazena por 1 hora
            break;
        case 'Form3':
            // Armazena os dados do formulário 3
            set_transient('form3_submission_data', $fields, 60 * 60); // Armazena por 1 hora
            break;
    }

}, 10, 2);

// Shortcode para exibir os resultados dos formulários
function show_form_results($atts) {
    $atts = shortcode_atts(['form' => ''], $atts, 'show_form_results');
    $form = $atts['form'];
    $data = get_transient($form . '_submission_data');

    if (!$data) {
        return '<p>Nenhum dado encontrado.</p>';
    }

    $audios_data = return_initial_audios_and_destiny_number($data['destiny_number']);

    ob_start();
    echo '<div class="audio-players">';

    // Renderizando áudios de introdução
    $introductions = $audios_data[0];
    if (isset($introductions['audio_introdutorio'])) {
        echo '<div class="audio-player">';
        echo '<audio controls src="' . esc_url($introductions['audio_introdutorio']) . '"></audio>';
        echo '<div class="subtitles" data-subtitles="' . esc_attr($introductions['legenda_intro']) . '"></div>';
        echo '</div>';
    }
    if (isset($introductions['pos_intro'])) {
        echo '<div class="audio-player">';
        echo '<audio controls src="' . esc_url($introductions['pos_intro']) . '"></audio>';
        echo '<div class="subtitles" data-subtitles="' . esc_attr($introductions['legenda_pos_intro']) . '"></div>';
        echo '</div>';
    }

    // Renderizando áudio do número de destino
    $destiny_audio = $audios_data[1];
    if (isset($destiny_audio['_audio_do_numero'])) {
        echo '<div class="audio-player">';
        echo '<audio controls src="' . esc_url($destiny_audio['_audio_do_numero']) . '"></audio>';
        echo '<div class="subtitles" data-subtitles="' . esc_attr($destiny_audio['_legenda_do_audio']) . '"></div>';
        echo '</div>';
    }

    echo '</div>';
    return ob_get_clean();
}
add_shortcode('show_form_results', 'show_form_results');

function add_custom_js() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var audioPlayers = document.querySelectorAll('.audio-player');

            audioPlayers.forEach(function(player) {
                var audio = player.querySelector('audio');
                var subtitleDiv = player.querySelector('.subtitles');
                var subtitlesString = subtitleDiv.getAttribute('data-subtitles');
                var subtitles = [];

                // Convertendo a string de legendas para um array de objetos
                try {
                    subtitlesString = subtitlesString.replace(/const subtitles = |;/g, '').trim();
                    subtitles = JSON.parse(subtitlesString.replace(/&quot;/g, '"'));
                } catch (e) {
                    console.error('Erro ao processar as legendas: ', e);
                }

                var timeoutHandles = [];

                audio.addEventListener('play', function() {
                    // Clear any existing timeouts
                    timeoutHandles.forEach(function(handle) {
                        clearTimeout(handle);
                    });
                    timeoutHandles = [];

                    subtitles.forEach(function(subtitle) {
                        var handle = setTimeout(function() {
                            subtitleDiv.textContent = subtitle.text;
                        }, subtitle.time * 1000);
                        timeoutHandles.push(handle);
                    });
                });

                audio.addEventListener('pause', function() {
                    timeoutHandles.forEach(function(handle) {
                        clearTimeout(handle);
                    });
                    timeoutHandles = [];
                });

                audio.addEventListener('ended', function() {
                    subtitleDiv.textContent = '';
                });
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'add_custom_js');


