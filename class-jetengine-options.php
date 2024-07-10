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
            $results[$repeater] = $this->audios[$repeater] ?? [];
        }

        return $results;
    }
}
