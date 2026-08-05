<?php
namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/v1')]
class TranslationController extends AbstractController
{
    // 1. Для /api/translations/ru/common
    #[Route('/translations/language/{locale}/{namespace}', name: 'api_translations_locale_namespace', methods: ['GET'])]
    public function getTranslationsWithNamespace(string $locale, string $namespace = 'common'): JsonResponse
    {
        return $this->getTranslations($locale, $namespace);
    }

    #[Route('/translations/languages', name: 'api_translations_languages', methods: ['GET'])]
    public function getLanguages(): JsonResponse
    {
        // Проверяем какие папки существуют
        $projectDir = $this->getParameter('kernel.project_dir');
        $translationsDir = $projectDir . '/translations/';

        $languages = [];
        if (is_dir($translationsDir)) {
            $dirs = scandir($translationsDir);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($translationsDir . $dir)) {
                    $languages[] = $dir;
                }
            }
        }

        // Если папок нет - возвращаем дефолтные
        if (empty($languages)) {
            $languages = ['ru', 'en', 'am'];
        }

        return $this->json($languages);
    }

    #[Route('/translations/language/{locale}', name: 'api_translations_language_locale', methods: ['GET'])]
    public function getTranslationsWithoutNamespace(string $locale): JsonResponse
    {
        return $this->getTranslations($locale, 'common');
    }

    private function getTranslations(string $locale, string $namespace = 'common'): JsonResponse
    {
        try {
            $projectDir = $this->getParameter('kernel.project_dir');
            $filePath = $projectDir . '/translations/' . $locale . '/' . $namespace . '.json';

            if (!file_exists($filePath)) {
                return $this->json([
                    'error' => 'File not found',
                    'path' => $filePath
                ], 404);
            }

            $content = file_get_contents($filePath);
            $data = json_decode($content, true);

            if ($data === null) {
                return $this->json([
                    'error' => 'Invalid JSON',
                    'path' => $filePath
                ], 500);
            }

            return $this->json($data);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
