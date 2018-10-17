<?php

namespace App\Service;

use Google\Cloud\Language\LanguageClient;

class GoogleService
{
    public function sentimentAnalysis($content) {
        $language = new LanguageClient([
            'projectId' => getenv('GOOGLE_PROJECT_ID')
        ]);

        $annotation = $language->analyzeSentiment($content);
        $sentiment = $annotation->sentiment();

        return $sentiment;
    }

}
