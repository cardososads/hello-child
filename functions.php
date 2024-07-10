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

// Processamento do formulário Form1
function process_form_01($record, $ajax_handler) {
    $raw_fields = $record->get('fields');

    if ($record->get_form_settings('form_id') === 'Form1') {
        $calculator = new NumerologyCalculator();
        $birth_date = sanitize_text_field($raw_fields['birth_date']);
        $_SESSION['first_name'] = sanitize_text_field($raw_fields['first_name']);
        $_SESSION['birth_date'] = $birth_date;
        $_SESSION['destiny_number'] = $calculator->calculateDestinyNumber($birth_date);

        // Adicionar var_dump para exibir os resultados
        echo '<pre>';
        echo "Resultados do Form1:\n";
        var_dump($_SESSION['first_name']);
        var_dump($_SESSION['birth_date']);
        var_dump($_SESSION['destiny_number']);
        echo '</pre>';

        $ajax_handler->add_response_data('redirect_url', home_url('/form-02'));
    }
}
add_action('elementor_pro/forms/new_record', 'process_form_01');

// Processamento do formulário Form2
function process_form_02($record, $ajax_handler) {
    $raw_fields = $record->get('fields');

    if ($record->get_form_settings('form_id') === 'Form2') {
        $calculator = new NumerologyCalculator();
        $full_name = sanitize_text_field($raw_fields['full_name']);
        $_SESSION['gender'] = sanitize_text_field($raw_fields['gender']);
        $_SESSION['full_name'] = $full_name;
        $_SESSION['expression_number'] = $calculator->calculateExpressionNumber($full_name);

        // Adicionar var_dump para exibir os resultados do Form1 e Form2
        echo '<pre>';
        echo "Resultados do Form1 e Form2:\n";
        var_dump($_SESSION['first_name']);
        var_dump($_SESSION['birth_date']);
        var_dump($_SESSION['destiny_number']);
        var_dump($_SESSION['gender']);
        var_dump($_SESSION['full_name']);
        var_dump($_SESSION['expression_number']);
        echo '</pre>';

        $ajax_handler->add_response_data('redirect_url', home_url('/form-03'));
    }
}
add_action('elementor_pro/forms/new_record', 'process_form_02');

// Processamento do formulário Form3
function process_form_03($record, $ajax_handler) {
    $raw_fields = $record->get('fields');

    if ($record->get_form_settings('form_id') === 'Form3') {
        $_SESSION['email'] = sanitize_email($raw_fields['email']);
        $_SESSION['marital_status'] = sanitize_text_field($raw_fields['marital_status']);

        // Adicionar var_dump para exibir todos os resultados
        echo '<pre>';
        echo "Resultados Finais:\n";
        var_dump($_SESSION['first_name']);
        var_dump($_SESSION['birth_date']);
        var_dump($_SESSION['destiny_number']);
        var_dump($_SESSION['gender']);
        var_dump($_SESSION['full_name']);
        var_dump($_SESSION['expression_number']);
        var_dump($_SESSION['email']);
        var_dump($_SESSION['marital_status']);
        echo '</pre>';

        // Redirecionar para a página de resultado ou outra ação
        $ajax_handler->add_response_data('redirect_url', home_url('/resultado'));
    }
}
add_action('elementor_pro/forms/new_record', 'process_form_03');
