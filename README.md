# Colaborative document reader
## 🚀 Features
- User registration and login with JWT
- Real-time document editing using WebSockets
- Version history tracking
- RESTful API integration between Laravel & React

## 🛠️ Tech Stack
- Laravel 9
- React 18
- Axios
- MySQL
- Laravel WebSockets
- Echo pusher on React, NPM package.

## ⚙️ Installation
### Backend
```bash
cd laravel-backend
composer install
php artisan migrate
php artisan serve
php artisan websocket:serve 


cd document-frontend
npm install
npm start


A **real-time collaborative document editor** built with **Laravel 9** (backend) and **React 18** (frontend).  
Aplication wich in real time tracking change the document between the loged user. If cathc that change version in same time. Will be back error status 409.
A 409 Conflict status code indicates that a request could not be completed because it conflicted with the current state of the target resource


Methods API REST CALL
-/api/register -new user
-/api/login  - login
-/api/documents  get all documents GET with POST create new documents
-/api/documents/{id} with PUT/POST edit document

On font end after login see list of documents if go on document name you will see document. If someone make change on text in editor will be reflectet and and shown in editor or view.


The bigest challenge for me was, working with react, because i didn't have expirence with that. Websocket install -> Echo Pusher for REACT.




