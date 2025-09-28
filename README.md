## Setup

Copy `.env.example` in the root as `.env`. These are environment variables for the docker container. Copy `backend/.env.example` as `backend/.env` as laravel requires environment variables in its root. I am still working on a work around for this.

Run `docker-compose up -d` in the project root to build your docker containers. This process will fail without `.env`.
