<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TranslationController extends AbstractController
{
    // 1. Для /api/translations/ru/common
    #[Route('/api/translations/{locale}/{namespace}', name: 'api_translations_with_namespace', methods: ['GET'])]
    public function getTranslationsWithNamespace(string $locale, string $namespace = 'common'): JsonResponse
    {
        return $this->getTranslations($locale, $namespace);
    }

    // 2. Для /api/translations/ru
    #[Route('/api/translations/{locale}', name: 'api_translations', methods: ['GET'])]
    public function getTranslationsWithoutNamespace(string $locale): JsonResponse
    {
        return $this->getTranslations($locale, 'common');
    }

    // 3. Для /translations/ru
    #[Route('/translations/{locale}', name: 'translations', methods: ['GET'])]
    public function getTranslationsOld(string $locale): JsonResponse
    {
        return $this->getTranslations($locale, 'common');
    }

    // 4. Для /languages
    #[Route('/languages', name: 'languages', methods: ['GET'])]
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

    // ОСНОВНАЯ ЛОГИКА - ОДИН РАЗ
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
