# Fullstack Boilerplate
A stack including CakePHP 5 (PHP 8.5) backend & Vue frontend

## Stack explanation

### Backend

The backend of the stack is written in PHP 8 using the CakePHP 5 framework.

It contains API management but also the build of the frontend for rendering.

Here are the included packages

- cakephp/authentication
- firebase/php-jwt

### Frontend

The frontend is in Vue instanced with Vite.

Here are the included packages

- @lucide/vue
- @tailwindcss/vite
- daisyui
- pinia
- tailwindcss
- typescript
- vue-i18n
- vue-router

## How to start

On the start point you can use those commands

- `npm run backend:serve` to start backend services
- `npm run frontend:dev` to start only frontend services
- `npm run frontend:build:prod` to build frontend into backend for production

### Notice

You can run all the command directly included into the backend or frontend like usually