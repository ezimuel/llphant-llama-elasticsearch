<?php
/**
 * Embedding in Elasticsearch using Ollama (Llama 3.2)
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use LLPhant\Chat\OllamaChat;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingGenerator\Ollama\OllamaEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Elasticsearch\ElasticsearchVectorStore;
use LLPhant\OllamaConfig;

# You can run ollama locally and install LLama3 from here: https://ollama.com/library/llama3
$config = new OllamaConfig();
$config->model = 'llama3.2';
$chat = new OllamaChat($config);

# Read PDF file
printf ("- Reading the PDF files\n");
$reader = new FileDataReader(dirname(__DIR__) . '/data/nobel_prize_physics_2024.pdf');
$documents = $reader->getDocuments();
printf("Number of PDF files: %d\n", count($documents));

# Document split
printf("- Document split\n");
$splitDocuments = DocumentSplitter::splitDocuments($documents, 800);
printf("Number of splitted documents (chunk): %d\n", count($splitDocuments));

# Embedding
printf("- Embedding\n");
$embeddingGenerator = new OllamaEmbeddingGenerator($config);
$embeddedDocuments = $embeddingGenerator->embedDocuments($splitDocuments);

# Read the .env file
$env = read_env_file(dirname(__DIR__) . '/elastic-start-local/.env');

# Elasticsearch
printf("- Index all the embeddings to Elasticsearch\n");
$es = (new ClientBuilder())::create()
    ->setHosts([$env['ES_LOCAL_URL']])
    ->setApiKey($env['ES_LOCAL_API_KEY'])
    ->build();

$elasticVectorStore = new ElasticsearchVectorStore($es, $indexName = 'nobel');
$elasticVectorStore->addDocuments($embeddedDocuments);

printf("Added %d documents in Elasticsearch with embedding included\n", count($embeddedDocuments));