# PetShop CRM - Sistema de Gestão

Sistema completo de gerenciamento para pet shops desenvolvido em **PHP, MySQL, HTML, CSS e JavaScript**.

## Características

### Autenticação e Segurança
- Sistema de login com autenticação por sessão
- Hash seguro de senhas com SHA-256
- Proteção contra acesso não autorizado
- Auditoria de atividades do usuário

### Módulos Principais

#### Dashboard
- Estatísticas gerais (clientes, pets, agendamentos)
- Receita do mês
- Próximos agendamentos
- Interface intuitiva com cards coloridos

#### Gestão de Clientes
- CRUD completo de clientes
- Busca e filtros
- Visualização de pets associados
- Histórico de agendamentos

#### Gerenciamento de Pets
- Registro de pets com dados completos
- Associação com clientes
- Raça, tipo, peso e data de nascimento
- Cards com design refinado

#### Agendamentos
- Criar, editar e deletar agendamentos
- Verificação de conflitos de horário
- Visualização em calendário
- Status: pendente, confirmado, concluído, cancelado

#### Serviços
- Cadastro de serviços oferecidos
- Preço e duração configurável
- Estatísticas de popularidade
- Soft delete (marca como inativo)

#### Faturas
- Criação de faturas automáticas ou manuais
- Controle de pagamentos
- Status: pendente, pago, cancelado
- Resumo financeiro mensal

#### Relatórios
- Receita mensal
- Serviços mais procurados
- Crescimento de clientes
- Taxa de ocupação
- Clientes mais ativos
- Exportação para CSV

## Paleta de Cores
- **Primário**: #0CA5B0 (Teal)
- **Secundário**: #4E3F30 (Marrom)
- **Claro**: #FEFEEB (Creme claro)
- **Fundo**: #F8F4E4 (Creme)
- **Acento**: #A5B3AA (Sage)

## Formatação Brasileira
- Datas: DD/MM/YYYY
- Moeda: R$ com vírgula decimal
- Telefone: Mascarado

## Instalação

1. **Criar o banco de dados**
   - Execute o arquivo `database.sql` no seu MySQL

2. **Configurar conexão**
   - Abra `config.php` e defina as credenciais do banco:
     \`\`\`php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'seu_usuario');
     define('DB_PASS', 'sua_senha');
     define('DB_NAME', 'petshop_crm');
     \`\`\`

3. **Acessar a aplicação**
   - Acesse `http://localhost/petshop_crm/login.php`
   - Credenciais demo: admin@petshop.com / 123456

## Estrutura de Pastas

\`\`\`
petshop_crm/
├── index.php              # Dashboard principal
├── login.php              # Página de login
├── logout.php             # Logout
├── profile.php            # Perfil do usuário
├── config.php             # Configuração e funções
├── auth-middleware.php    # Middleware de autenticação
├── customers-api.php      # API de clientes
├── appointments-api.php   # API de agendamentos
├── services-api.php       # API de serviços
├── invoices-api.php       # API de faturas
├── reports-api.php        # API de relatórios
├── styles.css             # Estilos CSS
├── app.js                 # JavaScript frontend
├── database.sql           # Script de banco de dados
└── README.md              # Este arquivo
\`\`\`

## Endpoints da API

### Clientes
- `GET /customers-api.php?acao=listar` - Listar clientes
- `POST /customers-api.php?acao=criar` - Criar cliente
- `PUT /customers-api.php?acao=atualizar` - Atualizar cliente
- `DELETE /customers-api.php?acao=deletar` - Deletar cliente

### Agendamentos
- `GET /appointments-api.php?acao=listar` - Listar agendamentos
- `POST /appointments-api.php?acao=criar` - Criar agendamento
- `PUT /appointments-api.php?acao=atualizar_status` - Atualizar status
- `GET /appointments-api.php?acao=horarios_disponiveis` - Obter horários livres

### Serviços
- `GET /services-api.php?acao=listar` - Listar serviços
- `POST /services-api.php?acao=criar` - Criar serviço
- `PUT /services-api.php?acao=atualizar` - Atualizar serviço

### Faturas
- `GET /invoices-api.php?acao=listar` - Listar faturas
- `POST /invoices-api.php?acao=criar` - Criar fatura
- `PUT /invoices-api.php?acao=atualizar_status` - Atualizar status
- `GET /invoices-api.php?acao=resumo` - Resumo financeiro

### Relatórios
- `GET /reports-api.php?acao=dashboard_resumo` - Dashboard resumido
- `GET /reports-api.php?acao=receita_mensal` - Receita mensal
- `GET /reports-api.php?acao=servicos_populares` - Serviços populares
- `GET /reports-api.php?acao=clientes_ativos` - Clientes mais ativos

## Recursos Técnicos

- **PHP**: 7.4+
- **MySQL**: 5.7+
- **HTML5**: Semântico
- **CSS3**: Flexbox, Grid
- **JavaScript**: Fetch API, LocalStorage
- **Padrões**: RESTful API, MVC

## Segurança

- Autenticação obrigatória em todas as páginas
- Proteção contra SQL Injection via escapar()
- Sessões PHP para manter estado
- Auditoria de atividades do usuário
- Validação de dados no servidor

## Suporte

Para dúvidas ou problemas, verifique:
1. Se o banco de dados foi criado corretamente
2. Se as credenciais em config.php estão corretas
3. Se o PHP possui extensão MySQLi habilitada
4. Os logs de erro do servidor web

---

Desenvolvido com ❤️ para gerenciamento de pet shops
