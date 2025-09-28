# A simple API for task management on Laravel

## API with CRUD operations for tasks:

- Create a task: POST /api/tasks (fields: title, description, status)
- View a list of tasks: GET /api/tasks (returns all tasks, paginated by 10 tasks)
- View a single task: GET /api/tasks/{id}
- Update a task: PUT /api/tasks/{id}
- Delete a task: DELETE /api/tasks/{id}

## Data validation:

- title (required, string, maximum 255 characters)
- description (optional, text)
- status (Boolean true/false field)

## Tests:

- The application returns a successful response
- The api returns a successful response
- Can get paginated list of tasks
- Can create a new task  
- Validates required fields when creating task
- Can show a specific task
- Returns 404 when task not found
- Can update an existing task
- Validates data when updating task
- Can delete a task
- Returns error when deleting non existent