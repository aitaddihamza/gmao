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
            ->withSystemPrompt("Vous êtes un expert en maintenance industrielle. Votre tâche est de générer des recommandations pertinentes pour résoudre des problèmes d'équipement")
            ->withPrompt("générer des recommandations pour le problème suivant : $description. Voici les informations sur l'équipement : $equipmentInfo")
            ->asText();


        return $recommandations->text;
    }
}
