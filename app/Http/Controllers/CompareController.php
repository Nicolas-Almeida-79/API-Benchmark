<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Cpu;
use App\Models\Gpu;

class CompareController extends Controller
{
    public function compareHardware(Request $request)
    {
        $type = $request->input('type');

        $hardware1Name = $request->input('hardware1');
        $hardware2Name = $request->input('hardware2');

        $result = ['success' => false, 'message' => 'Tipo de hardware inválido.'];

        switch ($type) {
            case 'cpu':
                $cpu1 = $this->getCpuByName($hardware1Name);
                $cpu2 = $this->getCpuByName($hardware2Name);
                if ($cpu1 && $cpu2) {
                    $comparisonResult = $this->compareCpu($cpu1, $cpu2);
                    $result = [
                        'success' => true,
                        'type' => 'cpu',
                        'hardware1' => $cpu1,
                        'hardware2' => $cpu2,
                        'comparison' => $comparisonResult
                    ];
                } else {
                    $result = ['success' => false, 'message' => 'CPU(s) não encontrada(s).'];
                }
                break;
            case 'gpu':
                $gpu1 = $this->getGpuByName($hardware1Name);
                $gpu2 = $this->getGpuByName($hardware2Name);
                if ($gpu1 && $gpu2) {
                    $comparisonResult = $this->compareGpu($gpu1, $gpu2);
                    $result = [
                        'success' => true,
                        'type' => 'gpu',
                        'hardware1' => $gpu1,
                        'hardware2' => $gpu2,
                        'comparison' => $comparisonResult
                    ];
                } else {
                    $result = ['success' => false, 'message' => 'GPU(s) não encontrada(s).'];
                }
                break;
        }

        return response()->json($result);
    }

    private function getCpuByName($name)
    {
        return Cpu::whereRaw('LOWER(model) = ?', [strtolower(trim($name))])->first();
    }

    private function getGpuByName($name)
    {
        return Gpu::whereRaw('LOWER(model) = ?', [strtolower(trim($name))])->first();
    }

    private function compareCpu($cpu1, $cpu2)
    {
        $score1 = 0;
        $score2 = 0;
        $differences = [];
        $specs = [];

        // Características com pesos e tipos (higher_better ou lower_better)
        $characteristics = [
            'boost' => ['weight' => 3.0, 'name' => 'Clock de Boost (GHz)', 'type' => 'higher_better'],
            'clock' => ['weight' => 2.5, 'name' => 'Clock Base (GHz)', 'type' => 'higher_better'],
            'cores' => ['weight' => 4.0, 'name' => 'Núcleos', 'type' => 'higher_better'],
            'threads' => ['weight' => 3.5, 'name' => 'Threads', 'type' => 'higher_better']
        ];
        
        // Verifica se as peças são idênticas
        $isExactMatch = strtolower(trim($cpu1->model)) === strtolower(trim($cpu2->model));

        // Adiciona todas as especificações para retornar
        $specs[] = ['name' => 'Modelo', 'value1' => $cpu1->model ?? 'N/A', 'value2' => $cpu2->model ?? 'N/A'];
        
        foreach ($characteristics as $key => $details) {
            $value1 = $cpu1->$key ?? null;
            $value2 = $cpu2->$key ?? null;
            
            // Adiciona a especificação à lista de retorno
            $specs[] = [
                'name' => $details['name'],
                'value1' => $value1 !== null ? $value1 : 'N/A',
                'value2' => $value2 !== null ? $value2 : 'N/A',
                'better' => null // Será preenchido abaixo se houver diferença
            ];
            
            // Pula se algum valor for nulo ou não numérico
            if (!is_numeric($value1) || !is_numeric($value2)) {
                continue;
            }

            $difference = $value1 - $value2;
            $type = $details['type'] ?? 'higher_better';
            
            // Inverte a lógica para características onde menor é melhor
            if ($type === 'lower_better') {
                $difference = -$difference;
            }

            if ($difference > 0) {
                // Calcula o peso dinâmico baseado na diferença percentual
                $percentDifference = $difference / max($value1, $value2) * 100;
                $dynamicWeight = $details['weight'] * (1 + min($percentDifference / 20, 1));
                $score1 += $dynamicWeight;
                
                // Marca qual é melhor na lista de especificações
                end($specs);
                $lastKey = key($specs);
                $specs[$lastKey]['better'] = 'hardware1';
            } elseif ($difference < 0) {
                $percentDifference = abs($difference) / max($value1, $value2) * 100;
                $dynamicWeight = $details['weight'] * (1 + min($percentDifference / 20, 1));
                $score2 += $dynamicWeight;
                
                // Marca qual é melhor na lista de especificações
                end($specs);
                $lastKey = key($specs);
                $specs[$lastKey]['better'] = 'hardware2';
            } else {
                // Valores iguais
                end($specs);
                $lastKey = key($specs);
                $specs[$lastKey]['better'] = 'equal';
            }

            if ($value1 != $value2) {
                $differences[] = [
                    'characteristic' => $details['name'],
                    'hardware1_value' => $value1,
                    'hardware2_value' => $value2,
                    'advantage' => $difference > 0 ? 'hardware1' : 'hardware2',
                    'significance' => min(round(abs($difference) / max($value1, $value2) * 100), 100) // Importância da diferença em %
                ];
            }
        }

        // Calcula a diferença percentual de pontuação
        $totalPossibleScore = array_sum(array_column($characteristics, 'weight')) * 2;
        $scoreDifference = abs($score1 - $score2);
        $winnerConfidence = $totalPossibleScore > 0 ? ($scoreDifference / $totalPossibleScore * 100) : 0;

        // Define o vencedor com base na pontuação
        $winner = 'tie';
        $isTechnicalTie = false;
        
        if ($winnerConfidence < 5) {
            // Empate técnico - diferença menor que 5%
            $isTechnicalTie = true;
        } else if ($score1 > $score2) {
            $winner = 'hardware1';
        } else if ($score2 > $score1) {
            $winner = 'hardware2';
        }

        return [
            'winner' => $winner,
            'is_technical_tie' => $isTechnicalTie,
            'winner_confidence' => round($winnerConfidence, 1),
            'score1' => round($score1, 2),
            'score2' => round($score2, 2),
            'differences' => $differences,
            'specs' => $specs,
            'message' => $this->getComparisonMessage($winner, $isTechnicalTie)
        ];
    }

    /**
     * Compara a arquitetura/geração de duas CPUs e ajusta as pontuações conforme necessário
     * 
     * @param object $cpu1 Primeira CPU para comparação
     * @param object $cpu2 Segunda CPU para comparação
     * @param float &$score1 Referência à pontuação da primeira CPU (será ajustada)
     * @param float &$score2 Referência à pontuação da segunda CPU (será ajustada)
     * @param array &$differences Referência ao array de diferenças (será atualizado)
     */
    /**
     * Gera uma mensagem amigável com o resultado da comparação
     */
    private function getComparisonMessage($winner, $isTechnicalTie, $isExactMatch = false)
    {
        if ($isExactMatch) {
            return "As peças são idênticas em termos de especificações técnicas.";
        }
        
        if ($isTechnicalTie) {
            return "As peças têm desempenho muito próximo. A diferença é mínima e pode não ser perceptível no uso prático. Recomenda-se verificar outros fatores como preço, eficiência energética e resfriamento para uma decisão final.";
        }
        
        switch ($winner) {
            case 'hardware1':
                return "O primeiro hardware tem melhor desempenho geral. Considere este modelo para um melhor desempenho em jogos e aplicações pesadas.";
            case 'hardware2':
                return "O segundo hardware tem melhor desempenho geral. Este modelo oferece melhor custo-benefício para a maioria dos usuários.";
                return "O segundo hardware tem melhor desempenho geral.";
            default:
                return "As peças têm desempenho equivalente.";
        }
    }
    
    private function compareArchitecture($cpu1, $cpu2, &$score1, &$score2, &$differences)
    {
        // Se não houver informação de arquitetura, não faz nada
        if (empty($cpu1->architecture) || empty($cpu2->architecture)) {
            return;
        }
        
        $arch1 = strtolower(trim($cpu1->architecture));
        $arch2 = strtolower(trim($cpu2->architecture));
        
        // Se as arquiteturas forem iguais, não precisa ajustar
        if ($arch1 === $arch2) {
            return;
        }
        
        // Tenta extrair números de geração (ex: "i7-10700K" -> 10, "Ryzen 5 3600" -> 3)
        $gen1 = $this->extractGeneration($arch1, $cpu1->model);
        $gen2 = $this->extractGeneration($arch2, $cpu2->model);
        
        // Se conseguiu extrair as gerações e são diferentes
        if ($gen1 !== null && $gen2 !== null && $gen1 != $gen2) {
            $genDifference = $gen1 - $gen2;
            $weight = 2.0; // Peso para diferença de geração
            
            if ($genDifference > 0) {
                $score1 += $weight * min($genDifference, 3); // Limita o bônus a 3 gerações
                $differences[] = [
                    'characteristic' => 'Geração/Arquitetura',
                    'hardware1_value' => $arch1 . ' (Gen ' . $gen1 . ')' ,
                    'hardware2_value' => $arch2 . ' (Gen ' . $gen2 . ')',
                    'advantage' => 'hardware1',
                    'significance' => min($genDifference * 15, 50) // Até 50% de significância
                ];
            } else {
                $score2 += $weight * min(abs($genDifference), 3);
                $differences[] = [
                    'characteristic' => 'Geração/Arquitetura',
                    'hardware1_value' => $arch1 . ' (Gen ' . $gen1 . ')' ,
                    'hardware2_value' => $arch2 . ' (Gen ' . $gen2 . ')',
                    'advantage' => 'hardware2',
                    'significance' => min(abs($genDifference) * 15, 50)
                ];
            }
        }
    }
    
    /**
     * Extrai o número da geração a partir da arquitetura ou modelo da CPU
     * 
     * @param string $architecture Arquitetura da CPU
     * @param string $model Modelo da CPU (usado como fallback)
     * @return int|null Número da geração ou null se não for possível determinar
     */
    private function extractGeneration($architecture, $model)
    {
        // Tenta extrair da arquitetura primeiro
        if (preg_match('/(\d{2,4})[a-z]?$/', $architecture, $matches)) {
            $gen = (int)$matches[1];
            // Se o número for grande (como 10900), provavelmente é o modelo completo, pega os primeiros dígitos
            return $gen > 20 ? (int)substr($gen, 0, -2) : $gen;
        }
        
        // Se não encontrou na arquitetura, tenta extrair do modelo
        if (preg_match('/(i[3579]|ryzen[\s\-_]?\d*[\s-])(\d{4})/i', $model, $matches)) {
            return (int)substr($matches[2], 0, 1); // Pega o primeiro dígito do modelo (ex: 10700K -> 1)
        }
        
        return null;
    }
    
    private function compareGpu($gpu1, $gpu2)
    {
        $score1 = 0;
        $score2 = 0;
        $differences = [];
        $specs = [];

        // Características com pesos e tipos (higher_better ou lower_better)
        $characteristics = [
            'boost_clock' => ['weight' => 2.5, 'name' => 'Clock de Boost (MHz)', 'type' => 'higher_better'],
            'base_clock' => ['weight' => 2.0, 'name' => 'Clock Base (MHz)', 'type' => 'higher_better'],
            'cuda_cores' => ['weight' => 3.5, 'name' => 'CUDA Cores', 'type' => 'higher_better'],
            'memory' => ['weight' => 3.0, 'name' => 'Memória (MB)', 'type' => 'higher_better']
        ];

        // Adiciona todas as especificações para retornar
        $specs[] = ['name' => 'Modelo', 'value1' => $gpu1->model ?? 'N/A', 'value2' => $gpu2->model ?? 'N/A'];
        
        foreach ($characteristics as $key => $details) {
            $value1 = $gpu1->$key ?? null;
            $value2 = $gpu2->$key ?? null;
            
            // Adiciona a especificação à lista de retorno
            $specs[] = [
                'name' => $details['name'],
                'value1' => $value1 !== null ? $value1 : 'N/A',
                'value2' => $value2 !== null ? $value2 : 'N/A',
                'better' => null // Será preenchido abaixo se houver diferença
            ];
            
            // Pula se algum valor for nulo ou não numérico
            if (!is_numeric($value1) || !is_numeric($value2)) {
                continue;
            }

            $difference = $value1 - $value2;
            $type = $details['type'] ?? 'higher_better';
            
            // Inverte a lógica para características onde menor é melhor
            if ($type === 'lower_better') {
                $difference = -$difference;
            }

            if ($difference > 0) {
                // Calcula o peso dinâmico baseado na diferença percentual
                $percentDifference = $difference / max($value1, $value2) * 100;
                $dynamicWeight = $details['weight'] * (1 + min($percentDifference / 20, 1));
                $score1 += $dynamicWeight;
                
                // Marca qual é melhor na lista de especificações
                end($specs);
                $lastKey = key($specs);
                $specs[$lastKey]['better'] = 'hardware1';
            } elseif ($difference < 0) {
                $percentDifference = abs($difference) / max($value1, $value2) * 100;
                $dynamicWeight = $details['weight'] * (1 + min($percentDifference / 20, 1));
                $score2 += $dynamicWeight;
                
                // Marca qual é melhor na lista de especificações
                end($specs);
                $lastKey = key($specs);
                $specs[$lastKey]['better'] = 'hardware2';
            } else {
                // Valores iguais
                end($specs);
                $lastKey = key($specs);
                $specs[$lastKey]['better'] = 'equal';
            }

            if ($value1 != $value2) {
                $differences[] = [
                    'characteristic' => $details['name'],
                    'hardware1_value' => $value1,
                    'hardware2_value' => $value2,
                    'advantage' => $difference > 0 ? 'hardware1' : 'hardware2',
                    'significance' => min(round(abs($difference) / max($value1, $value2) * 100), 100) // Importância da diferença em %
                ];
            }
        }

        // Calcula a diferença percentual de pontuação
        $totalPossibleScore = array_sum(array_column($characteristics, 'weight')) * 2;
        $scoreDifference = abs($score1 - $score2);
        $winnerConfidence = $totalPossibleScore > 0 ? ($scoreDifference / $totalPossibleScore * 100) : 0;

        // Define o vencedor com base na pontuação
        $winner = 'tie';
        $isTechnicalTie = false;
        
        if ($winnerConfidence < 5) {
            // Empate técnico - diferença menor que 5%
            $isTechnicalTie = true;
        } else if ($score1 > $score2) {
            $winner = 'hardware1';
        } else if ($score2 > $score1) {
            $winner = 'hardware2';
        }

        return [
            'winner' => $winner,
            'is_technical_tie' => $isTechnicalTie,
            'winner_confidence' => round($winnerConfidence, 1),
            'score1' => round($score1, 2),
            'score2' => round($score2, 2),
            'differences' => $differences,
            'specs' => $specs,
            'message' => $this->getComparisonMessage($winner, $isTechnicalTie)
        ];
    }
}