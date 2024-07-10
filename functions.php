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

function display_jetengine_data_shortcode() {
    $jet_engine_options = new JetEngine_Options();

    $introductions = $jet_engine_options->get_introductions();
    $all_repeaters = $jet_engine_options->get_all_repeaters();

    ob_start();

    echo '<pre>';
    echo "Introduções:\n";
    print_r($introductions);

    echo "\nRepeaters:\n";
    print_r($all_repeaters);
    echo '</pre>';

    return ob_get_clean();
}

add_shortcode('display_jetengine_data', 'display_jetengine_data_shortcode');

// Hook para processar o envio dos formulários
add_action('elementor_pro/forms/new_record', function ($record, $handler) {
    // Certifique-se de que o handler é o Elementor Forms
    if ('elementor_pro' !== $handler->get_id()) {
        return;
    }

    // Obtenha os dados do formulário
    $form_name = $record->get_form_settings('form_name');
    $fields = $record->get('fields');

    // Instancia a classe de cálculo
    $calculator = new NumerologyCalculator();

    // Armazena os dados do formulário usando transients para acesso global
    switch ($form_name) {
        case 'Form1':
            // Realiza o cálculo do número de destino
            $data = [];
            foreach ($fields as $id => $field) {
                $data[$id] = $field['value'];
            }
            $data['destiny_number'] = $calculator->calculateDestinyNumber($data['birth_date']);
            set_transient('form1_submission_data', $data, 60 * 60); // Armazena por 1 hora

            // Recupera os áudios de introdução e destino
            $jet_engine_options = new JetEngine_Options();
            $introductions = $jet_engine_options->get_introductions();
            $all_repeaters = $jet_engine_options->get_all_repeaters();

            // Filtra o áudio correspondente ao número de destino
            if (isset($all_repeaters['_numeros_destino_516'][$data['destiny_number']])) {
                $data['destiny_audio'] = $all_repeaters['_numeros_destino_516'][$data['destiny_number']];
            } else {
                $data['destiny_audio'] = 'Nenhum áudio encontrado para o número de destino.';
            }

            // Armazena os áudios de introdução
            $data['introductions'] = $introductions;

            set_transient('form1_submission_data', $data, 60 * 60); // Armazena por 1 hora
            break;

        case 'Form2':
            // Realiza o cálculo do número de expressão
            $data = [];
            foreach ($fields as $id => $field) {
                $data[$id] = $field['value'];
            }
            $data['expression_number'] = $calculator->calculateExpressionNumber($data['full_name']);
            set_transient('form2_submission_data', $data, 60 * 60); // Armazena por 1 hora
            break;

        case 'Form3':
            // Armazena os dados do formulário 3
            $data = [];
            foreach ($fields as $id => $field) {
                $data[$id] = $field['value'];
            }
            set_transient('form3_submission_data', $data, 60 * 60); // Armazena por 1 hora
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

    ob_start();
    echo '<pre>';
    echo "Áudios de Introdução:\n";
    print_r($data['introductions']);

    echo "\nÁudio do Número de Destino:\n";
    print_r($data['destiny_audio']);
    echo '</pre>';
    return ob_get_clean();
}
add_shortcode('show_form_results', 'show_form_results');
