<?php
namespace Dotknock\AskDb;

use OpenAI\Client;

class AskDb
{
    protected array $modelsConfig = [];
    protected Client $openAI;

    public function __construct()
    {
        $this->openAI = Client::fromEnv(); // expects OPENAI_API_KEY in env
    }

    public function registerModel(string $modelClass, string $description, array $allowedFields = [], array $disallowedFields = [])
    {
        $this->modelsConfig[$modelClass] = [
            'description' => $description,
            'allowed' => $allowedFields,
            'disallowed' => $disallowedFields,
        ];
    }

    protected function getAllowedFields(string $modelClass): array
    {
        if (!isset($this->modelsConfig[$modelClass])) return [];
        return array_diff(
            $this->modelsConfig[$modelClass]['allowed'] ?? [],
            $this->modelsConfig[$modelClass]['disallowed'] ?? []
        );
    }

    /**
     * Generate safe Eloquent query using OpenAI GPT-4
     *
     * @param string $prompt
     * @return string
     */
    public function generateQueryWithAI(string $prompt): string
    {
        // Prepare model metadata for prompt
        $modelsSummary = [];
        foreach ($this->modelsConfig as $model => $config) {
            $allowed = implode(', ', $this->getAllowedFields($model));
            $desc = $config['description'] ?? '';
            $shortModel = class_basename($model);
            $modelsSummary[] = "- $shortModel: $desc. Allowed fields: $allowed";
        }

        $systemMessage = "You are an expert Laravel developer. Given a user natural language request and the available models with their descriptions and allowed fields, generate a safe Laravel Eloquent query in PHP. 
- Use only the allowed fields.
- Use only the models listed.
- Return only valid PHP code with Eloquent queries.
- If the request cannot be fulfilled or tries to access disallowed fields or unknown models, respond only with: 
'Sorry, I cannot help with that request. Please contact the administrator for further assistance.'
";

        $userMessage = "Available models and allowed fields:\n" . implode("\n", $modelsSummary) . "\n\nUser request:\n" . $prompt;

        $response = $this->openAI->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'temperature' => 0,
            'max_tokens' => 300,
        ]);

        $reply = $response->choices[0]->message->content ?? '';

        return trim($reply);
    }
}
