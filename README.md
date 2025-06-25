## Task Manger Built using laravel with a Kanban Board



A simple yet powerful task management app built with Laravel featuring a drag-and-drop Kanban board. Users can create tasks, assign priorities, update progress, and manage workflows efficiently.

✨ Features
	•	User authentication
	•	Task CRUD operations
	•	Kanban board with drag-and-drop
	•	Task categories (To Do, In Progress, Done)
	•	Responsive UI using Tailwind CSS

# Screenshots
<img width="1680" alt="image" src="https://github.com/user-attachments/assets/7ebebe55-71fa-479c-9a07-1cc57dd5bbba" />

<img width="1680" alt="image" src="https://github.com/user-attachments/assets/3ef136a9-1b18-49bb-a2a3-091cc551698d" />

<img width="1680" alt="image" src="https://github.com/user-attachments/assets/997b5356-98d6-4da9-8030-a568180d1144" />

# Instructions
1. Clone the project
```
[git clone https://github.com/favourdev1/task-manager.git](https://github.com/favourdev1/TaskManager.git)
cd task-manager
```
2.	Install dependencies
```
composer install
npm install && npm run dev
```

# 3. Set up environment
```
cp .env.example .env
php artisan key:generate
```

# 4. Configure your database in .env
```
# then run migrations
php artisan migrate
```

# 5. Start the local server
```
php artisan serve
```



Visit: http://localhost:8000
