<?php
/**
 * RAG architecture with Llama3.2 and Elasticsearch
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use LLPhant\Chat\OllamaChat;
use LLPhant\Embeddings\EmbeddingGenerator\Ollama\OllamaEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Elasticsearch\ElasticsearchVectorStore;
use LLPhant\OllamaConfig;
use LLPhant\Query\SemanticSearch\QuestionAnswering;

# Ollama with Llama3
$config = new OllamaConfig();
$config->model = 'llama3.2';
$config->modelOptions = [
    'options' => [
        'temperature' => 0
    ]
];
$chat = new OllamaChat($config);

# Embedding
$embeddingGenerator = new OllamaEmbeddingGenerator($config);

# Read the .env file
$env = read_env_file(dirname(__DIR__) . '/elastic-start-local/.env');

# Elasticsearch
$es = (new ClientBuilder())::create()
    ->setHosts([$env['ES_LOCAL_URL']])
    ->setApiKey($env['ES_LOCAL_API_KEY'])
    ->build();

$elasticVectorStore = new ElasticsearchVectorStore($es, $indexName = 'nobel');

# RAG
$qa = new QuestionAnswering(
    $elasticVectorStore,
    $embeddingGenerator,
    $chat
);

$answer = $qa->answerQuestion('Who won the Nobel Prize in Physics in 2024?');
printf("-- Answer:\n%s\n", $answer);
printf("\n");
$retrievedDocs = $qa->getRetrievedDocuments();
printf("We used %d documents to answer the question, as follows:\n\n", count($retrievedDocs));
foreach ($qa->getRetrievedDocuments() as $doc) {
    printf("-- Document: %s\n", $doc->sourceName);
    printf("-- Hash: %s\n", $doc->hash);
    printf("-- Content of %d characters, extract: %s...\n\n", strlen($doc->content), substr($doc->content, 0, 100));
}
