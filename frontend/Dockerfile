FROM node:22-bookworm-slim
WORKDIR /app

# copy ONLY package.json so a fresh lock is created
COPY package.json ./

RUN npm i -g npm@11 && npm install --no-audit --progress=false

COPY . .

RUN npm run postinstall

EXPOSE 3000
CMD ["npm", "run", "dev"]
