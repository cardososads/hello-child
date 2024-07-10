<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class JetEngine_Options {
    private $audios;

    public function __construct() {
        $this->audios = get_option('_audios');
    }

    public function get_introductions() {
        return [
            'audio_introdutorio' => $this->audios['_audio-introdutorio'] ?? '',
            'legenda_intro' => $this->audios['_legenda-intro'] ?? '',
            'pos_intro' => $this->audios['_pos-intro'] ?? '',
            'legenda_pos_intro' => $this->audios['_legenda-pos-intro'] ?? ''
        ];
    }

    public function get_all_repeaters() {
        $repeaters = [
            '_numeros_destino_516',
            '_numeros_expressao_masculinos',
            '_numeros_expressao_femininos',
            '_numeros_expressao_sem_genero',
            '_numeros_motivacao_masculino_casado',
            '_numeros_motivacao_masculino_solteiro',
            '_numeros_motivacao_feminino_casada',
            '_numeros_motivacao_feminino_solteira',
            '_numeros_motivacao_outros'
        ];

        $results = [];

        foreach ($repeaters as $repeater) {
            $results[$repeater] = $this->audios[$repeater] ?? [];
        }

        return $results;
    }
}
