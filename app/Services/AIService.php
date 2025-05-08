<?php

namespace App\Services;

use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

class AIService
{
    public function generateRecommendations($description, $equipmentInfo)
    {
        $recommandations = Prism::text()
            ->using(Provider::OpenAI, 'google/gemma-3-12b-it')
            ->withSystemPrompt("Votre tâche est de fournir des recommandations claires et concises pour résoudre des problèmes d'équipement. Les réponses doivent être structurées sous forme de points.")
            ->withPrompt("Générez des recommandations pour le problème suivant : $description. Voici les informations sur l'équipement : $equipmentInfo. Formatez la réponse comme suit : 
            - Point : [Description concise]
            - Point : [Description concise]
            ...")
            ->asText();

        return $recommandations->text;
    }
}
