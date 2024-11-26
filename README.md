# Build a RAG in PHP with LLPhant, Llama3.2 and Elasticsearch

This is an example of **Retrieval-Augmented Generation (RAG)** in PHP using [LLPhant](https://github.com/theodo-group/LLPhant), [Llama 3.2][Llama 3.2](https://www.llama.com/) and [Elasticsearch](https://github.com/elastic/elasticsearch).

To show the usage of a RAG system, we want Llama 3.2 answering to the following question:

*Who won the Nobel Prize in Physics in 2024?*

If we ask to Llama 3.2 this question the model will reply with a message as follows:

*I don't have information on the 2024 Nobel Prize winners, as my knowledge cutoff is December 2023.*

We will show how to augment the knowledge of Llama 3.2 using the RAG architecture.

## Install Llama3.2

You can install [Llama 3.2](https://www.llama.com/) using [ollama](https://ollama.com/).

For installing ollama on Linux, run the following command:

```bash
curl -fsSL https://ollama.com/install.sh | sh
```

If you are using macOS or Windows use the [download](https://ollama.com/download) page.

We suggest to install Llama3.2-1B or 3B that requires less CPU/GPU and RAM.

For installing Llama3.2-3B use the following command:

```bash
ollama run llama3.2:3b 
```

You can start interacting to the LLama3.2 model using a chat. To exit, write `/bye` in the chat.

## Install Elasticsearch

```bash
curl -fsSL https://elastic.co/start-local | sh
```

This script will install Elasticsearch and Kibana using a `docker-compose.yml` file stored in
`elastic-start-local` folder.

Elasticsearch and Kibana will run locally at http://localhost:9200 and http://localhost:5601.

All the settings of Elasticsearch and Kibana are stored in the `elastic-start-local/.env` file.

## Install the RAG example

You can install the PHP RAG example using [composer](https://getcomposer.org/), as follows:

```bash
composer install
```

If you don't have composer installed you can download it from [here](https://getcomposer.org/download/).

## Add the Nobel Prize 2024 knowledge

We need to store the knowledge about the Nobel Prize of 2024. We need to use a vector database for storing the
[embeddings](https://www.elastic.co/what-is/vector-embedding), the mathematical representation of a sentence using an array of float numbers. 

If you are not familiar with the RAG architecture, you can watch [this introduction](https://www.youtube.com/watch?v=exQR-eXRDvU).

The information about the Nobel Prize of 2024 is stored in a PDF file [data/nobel_prize_physics_2024.pdf](data/nobel_prize_physics_2024.pdf). This is PDF version of the content of this web page:
https://www.nobelprize.org/prizes/physics/2024/popular-information/.

We will use LLPhant to do the following steps:
- read the PDF file;
- extract the text from the PDF;
- split the document in chunk of 800 characters;
- generate the embeddings using Llama 3.2 model;
- store the embeddings in Elasticsearch;

When we will have the chunks stored in Elasticsearch we can implement the RAG architecture using the
[Question Answering feature](https://github.com/theodo-group/LLPhant?tab=readme-ov-file#question-answering) of LLPhant.

The embeddings is provided using the `src/embedding.php` and the question answering is implemented in `src/qa.php`.
You need to execute the `embedding` first and the `qa` after, as follows:

```bash
php src/embedding.php
php src/qa.php
```

The output of `src/qa.php` will be something as follows:

```
-- Answer:
The winners of the Nobel Prize in Physics for 2024 are John J. Hopfield and Geoffrey Hinton.

We used 4 documents to answer the question, as follows:

-- Document: ./data/nobel_prize_physics_2024.pdf
-- Hash: 899fd14b31a9c2989ec584ff7fe766bbb6907378fd14083f5f6933fcb7ec75f1
-- Content of 485 characters, extract: Professor at  University of Toronto, Canada...
```

We expanded the knowledge of Llama 3.2, now it knows who won the Nobel Prize in Physics 2024!

# Copyright

Copyright by [Enrico Zimuel](https://www.zimuel.it/), 2024.