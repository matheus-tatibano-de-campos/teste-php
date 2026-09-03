Sistema de Ordem de Serviços — JM Informática
Projeto desenvolvido para o processo de avaliação técnica da Titan Software.

O sistema permite gerenciar ordens de serviço prestadas por funcionários, com controle de autenticação, dashboard de indicadores, filtros de busca, cadastro/edição/exclusão de serviços, cálculo automático de comissões e notificação por e-mail.

Desenvolvido em PHP nativo seguindo o padrão MVC e Orientação a Objetos, sem o uso de frameworks ou Composer.

Pré-requisitos
PHP 7.4 ou superior (com extensões PDO e pdo_mysql ativas)
MySQL / MariaDB (ex: XAMPP)
Mailpit (opcional, utilizado para interceptar os e-mails em ambiente de desenvolvimento local)
Como rodar o projeto
1. Banco de Dados
Inicie o serviço do MySQL (pelo painel do XAMPP ou via terminal).
No phpMyAdmin ou cliente MySQL de sua preferência, importe o arquivo:
database/schema.sql (cria o banco jm_servicos, as tabelas e o usuário inicial).
(Opcional) Para já carregar serviços de exemplo no dashboard, importe também:
database/seed_services.sql.
2. Configuração
Abra o arquivo config/config.php e verifique as configurações de banco e a URL base:

DB_HOST: localhost
DB_NAME: jm_servicos
DB_USER: root
DB_PASS: vazio por padrão
BASE_URL: ajuste conforme a pasta onde você colocar o projeto:
Se colocar em C:\xampp\htdocs\teste-php\teste-php\: mantenha o padrão 'http://localhost/teste-php/teste-php/'
Se colocar em C:\xampp\htdocs\teste-php\: altere para 'http://localhost/teste-php/'
Se rodar via terminal (php -S): altere para 'http://localhost:8080/'
3. Execução
Via Apache (XAMPP): Copie a pasta para o seu htdocs e acesse no navegador a URL configurada no BASE_URL (ex: http://localhost/teste-php/teste-php/ ou http://localhost/teste-php/).

Via servidor embutido do PHP: Na raiz do projeto, execute:

php -S localhost:8080
(Lembrando de colocar define('BASE_URL', 'http://localhost:8080/'); no config/config.php)

Acesso ao Sistema (Usuário de Teste)
O script SQL já cria um usuário pronto para acesso:

E-mail: jose@jm.com
Senha: password
Teste de Envio de E-mail (Mailpit)
Ao finalizar um serviço pendente, o sistema registra a data de finalização, calcula a comissão correspondente e dispara um e-mail de notificação para o funcionário responsável via função nativa mail().

Para capturar e visualizar esse e-mail em ambiente local sem precisar de um servidor SMTP real:

Baixe e execute o Mailpit (mailpit.exe).
No arquivo php.ini do seu PHP/XAMPP, configure a seção de e-mail para apontar para a porta local do Mailpit:
[mail function]
SMTP = localhost
smtp_port = 1025
sendmail_from = sistema@localhost
Reinicie o Apache.
Ao finalizar qualquer serviço no dashboard, abra o navegador em http://localhost:8025 para ver a mensagem recebida.
Nota: Caso o serviço de e-mail local não esteja configurado, a finalização do serviço e o cálculo da comissão continuam funcionando normalmente no banco.

Regras de Negócio Implementadas
Comissões:
Serviços até R$ 1.000,00: 5%
Serviços entre R$ 1.000,01 e R$ 10.000,00: 10%
Serviços acima de R$ 10.000,00: 20%
Status do Serviço:
Sem data de finalização (finished_at = NULL): Pendente
Com data de finalização preenchida: Finalizado
Segurança:
Senhas com hash password_hash().
Consultas com PDO Prepared Statements contra SQL Injection.
Proteção de rotas com validação de sessão ativa e proteção CSRF em formulários POST.
