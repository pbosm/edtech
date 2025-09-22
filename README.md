# EdTech Infra Starter (Laravel + Nuxt + Docker)

## Resumo do projeto
```bash
Núcleo de um Sistema de Gestão de Cursos Online (EdTech). 
A solução entrega uma API REST em Laravel para cursos, alunos e matrículas, 
e um frontend em Nuxt 3 (Composition API / <script setup>) para dashboard, listagens, formulários e detalhes. 
Todo o ambiente roda em Docker Compose, com MySQL e Nginx.
````

## Como rodar
```bash
# 1) Clonar
git clone https://github.com/pbosm/edtech.git edtech
cd edtech

# 2) Subir a infra e fazer o build das imagens 
#    (o projeto usa start.sh no backend e start.sh no frontend para preparar tudo:
#     .env, deps, APP_KEY, wait pelo MySQL, migrate e seed condicional no backend;
#     deps e dev server no frontend)
docker compose up -d --build

# 3) Ver logs (opcional)
docker logs -f edtech-frontend
docker logs -f edtech-backend
````

## Endpoints backend
```bash
# Dashboard
GET    /courses/dashboard

# Courses
GET    /courses                 # ?page=1&per_page=10&filterMsg=texto
GET    /courses/{id}
GET    /courses/{id}/students   # ?page=1&per_page=10
POST   /courses                 # { name, description?, duration_hours }
PUT    /courses/{id}            # { name, description?, duration_hours }
DELETE /courses/{id}

# Students
GET    /students                # ?page=1&per_page=10&filterMsg=texto
GET    /students/{id}
POST   /students                # { name, email, cpf(mask: XXX.XXX.XXX-XX) }

# Enrollments
POST   /enrollments             # { student_id, course_id }
PUT    /enrollments/{id}/progress  # { progress }
````

## Rotas frontend
```bash
/                          -> Dashboard com cards e gráfico "Alunos por curso"
                           • Totais: cursos, alunos
                           • Média de horas por curso
                           • Top 10 cursos (chart)

/courses                   -> Listagem de cursos
                           • Filtros: busca por nome/descrição ?filterMsg=
                             paginação (?page, ?per_page)
                           • Ações por linha: Ver, Editar, Excluir

/courses/create            -> Criar curso
                           • Campos: name, description (opcional), duration_hours
                           • Validações de front + exibição de erros do backend
                           • Redireciona para /courses/{id} ao sucesso

/courses/:id               -> Detalhes do curso
                           • Mostra informações do curso
                           • Lista de alunos matriculados (com paginação)
                           • Ações: Voltar, Editar, Excluir

/courses/:id/edit          -> Editar curso
                           • Form idêntico ao create, preenchido com os dados atuais
                           • Exibe validações de front + backend

/students                  -> Listagem de alunos
                           • Filtro de busca enviado como ?filterMsg=...
                           • Paginação (?page, ?per_page)
                           • (Opcional) Colunas: nome, e-mail, CPF, criado em

/students/create           -> Criar aluno
                           • Campos: name, email, cpf (com máscara XXX.XXX.XXX-XX)
                           • Validações de front + exibição de erros do backend
                           • Redireciona para /students ao sucesso

/* (404)                   -> Página “Não encontrado”
                            • Mensagem amigável e botão para voltar ao dashboard
````


