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
    print_r(return_initial_audios_and_destiny_number($data['destiny_number']));
    $audios_data = return_initial_audios_and_destiny_number($data['destiny_number']);
    $destiny_audio = $audios_data[1];

    ob_start();
    echo '<div class="audio-players">';

    // Renderizando áudio do número de destino
    if (isset($destiny_audio['_audio_do_numero'])) {
        echo '<div class="audio-player">';
        echo '<audio controls src="' . esc_url($destiny_audio['_audio_do_numero']) . '"></audio>';
        echo '<div class="subtitles"></div>';
        echo '</div>';
    }

    echo '</div>';

    // Passando legendas para o JavaScript
    echo '<script>';
    echo 'const subtitles = ' . json_encode($destiny_audio['_legenda_do_audio']) . ';';
    echo '</script>';

    return ob_get_clean();
}
add_shortcode('show_form_results', 'show_form_results');





