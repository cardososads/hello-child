<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class JetEngine_Options {
    private $options_slug;

    public function __construct($options_slug) {
        $this->options_slug = $options_slug;
    }

    public function get_introductions() {
        $introductions = [];

        $introductions['audio_introdutorio'] = get_option('_audio-introdutorio');
        $introductions['legenda_intro'] = get_option('_legenda-intro');
        $introductions['pos_intro'] = get_option('_pos-intro');
        $introductions['legenda_pos_intro'] = get_option('_legenda-pos-intro');

        return $introductions;
    }

    public function get_repeater($repeater_name) {
        return get_option($repeater_name);
    }

    public function get_all_repeaters() {
        $repeaters = [
            'numeros_destino_516',
            'numeros_expressao_masculinos',
            'numeros_expressao_femininos',
            'numeros_expressao_sem_genero',
            'numeros_motivacao_masculino_casado',
            'numeros_motivacao_masculino_solteiro',
            'numeros_motivacao_feminino_casada',
            'numeros_motivacao_feminino_solteira',
            'numeros_motivacao_outros'
        ];

        $results = [];

        foreach ($repeaters as $repeater) {
            $results[$repeater] = get_option($repeater);
        }

        return $results;
    }
}
