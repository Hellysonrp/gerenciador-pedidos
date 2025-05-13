## Sistema de Gerenciamento de Pedidos

Sistema simples de cadastro de produtos e criação de pedidos, com controle básico de estoque.

Projeto da disciplina de Arquitetura de Software do curso de Engenharia de Software da Universidade Tecnológica Federal do Paraná - UTFPR, Campus Dois Vizinhos.

## Execução do Projeto

É necessário ter uma instalação do PHP com todas as extensões necessárias para o Laravel, além do Composer.

As bibliotecas JavaScript também dependem do NPM ou equivalente para serem gerenciadas.

Recomenda-se instalar o ambiente [conforme mencionado na documentação do Laravel](https://laravel.com/docs/12.x/installation#installing-php).

Após instalação do ambiente, instale todas as dependências com `composer install` e `npm install`. Execute o projeto com o comando `composer run dev`.

É necessário também renomear o arquivo `.env.example` para `.env`, configurar o que for necessário (ex: dados de banco de dados) e executar também o comando `php artisan key:generate` para gerar a chave de criptografia do projeto.
