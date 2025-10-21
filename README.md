# Eventware

The Event Syncer simplifies personal schedule management by automatically syncing events from various sources to your
preferred calendar tool. Stay updated on sports, tech, UFC, and Formula 1 events effortlessly with our intuitive
platform. Say goodbye to missed appointments – Event Syncer keeps you in the loop.

## Event storming

### Event: New user registers with Event Syncer

* **Command:** User fills out registration form.
* **Event:** System receives new user's data.
* **Reaction:** System creates a new user account.

### Event: User subscribes to a shared calendar

* **Command:** User selects a shared calendar for subscription.
* **Event:** System receives user's subscription request.
* **Reaction:** System checks availability of the calendar and adds the user to the subscriber list.

### Event: User creates an event in their own calendar

* **Command:** User adds a new event to their personal calendar.
* **Event:** System receives details of the new event from the user.
* **Reaction:** System updates the user's calendar with the new event.

### Event: System checks for updates in shared calendars

* **Command:** System performs periodic check on shared calendars.
* **Event:** System identifies new events or updates in shared calendars.
* **Reaction:** System updates calendars of subscribed users with the new events or changes.

### Event: User unsubscribes from a shared calendar

* **Command:** User requests to unsubscribe from a shared calendar.
* **Event:** System receives user's unsubscription request.
* **Reaction:** System removes the user from the subscriber list of the shared calendar.

### Event: Update of an event in a shared calendar

* **Command:** An event in a shared calendar is updated.
* **Event:** System receives the event update from the shared calendar.
* **Reaction:** System updates calendars of subscribed users with the changes in the event.

### Event: System identifies event conflicts

* **Command:** System compares events in calendars of subscribed users.
* **Event:** System detects overlapping schedules between events.
* **Reaction:** System notifies users about the conflict and provides resolution options.

## Suggested Technologies

| Technology                                                                    | Description                                                          |
|-------------------------------------------------------------------------------|----------------------------------------------------------------------|
| [ICalendar](https://icalendar.org/)                                           | The iCalendar format is a common standard for sharing calendar data. |
| [markuspoerschke/iCal (PHP Library)](https://github.com/markuspoerschke/iCal) | A simple library to parse and generate iCal files.                   |

## References

- https://laravel-news.com/ical-library-for-php
- https://github.com/markuspoerschke/iCal
- https://icalendar.org/
- https://groups.google.com/g/sabredav-discuss/c/-CdR7z4PX-I?pli=1

## 📋 Pré-requisitos

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## 🚀 Instalação

1. Clone o repositório:

```bash
git clone git@github.com:medeirosnacio/eventware.git
cd eventware
```

1. Copie o arquivo de ambiente:

```bash
make setup
```

## 📂 Estrutura do Projeto

```bash
sorteio/
├── docker-compose.yml       # Configuração dos serviços Docker
├── Dockerfile               # Imagem PHP customizada
├── docker-entrypoint.sh     # Script de inicialização
├── docker/
│   └── nginx/
│       └── nginx.conf       # Configuração do Nginx
└── php.ini                  # Configurações customizadas do PHP
```

## 🐳 Comandos Docker

Para iniciar o servidor:

```bash
docker-compose up -d
```

Para parar e remover os containers:

```bash
docker-compose down
```

Entrar no container da aplicação:

```bash
docker-compose exec app bash
```

## Observações

Certifique-se de estar no diretório correto do projeto antes de executar os comandos.

Caso precise ver os logs dos containers, utilize:

```bash
docker-compose logs -f
```

Para reconstruir a imagem após alterações no Dockerfile, use:

```bash
docker-compose up -d --build --force-recreate
```

## 🌐 Acessando a Aplicação

Após iniciar os containers, acesse:

> Aplicação: http://localhost:8080

## 📝 Notas Importantes

- Certifique-se de que as portas 8080 e 9000 estejam disponíveis
- Os volumes mapeiam o código local para /app no container
- As permissões dos diretórios storage e bootstrap/cache são configuradas automaticamente
