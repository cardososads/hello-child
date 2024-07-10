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
            'audio_introdutorio' => $this->clean_string($this->audios['_audio-introdutorio'] ?? ''),
            'legenda_intro' => $this->clean_string($this->audios['_legenda-intro'] ?? ''),
            'pos_intro' => $this->clean_string($this->audios['_pos-intro'] ?? ''),
            'legenda_pos_intro' => $this->clean_string($this->audios['_legenda-pos-intro'] ?? '')
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
            if (isset($this->audios[$repeater])) {
                $results[$repeater] = $this->simplify_repeater($this->audios[$repeater]);
            } else {
                $results[$repeater] = [];
            }
        }

        return $results;
    }

    private function clean_string($string) {
        return stripslashes($string);
    }

    private function simplify_repeater($repeater) {
        $simplified = [];
        foreach ($repeater as $item) {
            $numero = $item['numero'] ?? null;
            if ($numero !== null) {
                $simplified[$numero] = [
                    '_audio_do_numero' => $this->clean_string($item['_audio_do_numero'] ?? ''),
                    '_legenda_do_audio' => $this->clean_string($item['_legenda_do_audio'] ?? '')
                ];
            }
        }
        return $simplified;
    }
}
