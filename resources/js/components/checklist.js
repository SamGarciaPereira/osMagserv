let tasks = [];
let checklistInput, container, newTaskInput;

function renderTasks() {
    container.innerHTML = "";

    const statusSelect = document.getElementById('status');
    const currentStatus = statusSelect ? statusSelect.value : '';
    const canDelete = !['Validado', 'Enviado', 'Aprovado'].includes(currentStatus);

    tasks.forEach((task, index) => {
        const li = document.createElement("li");
        li.className =
            "flex items-center justify-between p-3 bg-gray-50 rounded-lg border " +
            (task.completed
                ? "border-green-200 bg-green-50"
                : "border-gray-200");

        const leftDiv = document.createElement("div");
        leftDiv.className = "flex items-center gap-3";

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.checked = task.completed;
        checkbox.className =
            "w-5 h-5 text-blue-600 rounded focus:ring-blue-500 cursor-pointer";

        checkbox.addEventListener("change", () => {
            task.completed = !task.completed;
            updateState();
        });

        const span = document.createElement("span");
        span.textContent = task.text;
        span.className = task.completed
            ? "text-gray-400 line-through decoration-2"
            : "text-gray-700";

        leftDiv.appendChild(checkbox);
        leftDiv.appendChild(span);
        li.appendChild(leftDiv);

        if (canDelete) {
            const deleteBtn = document.createElement("button");
            deleteBtn.type = "button";
            deleteBtn.innerHTML = '<i class="bi bi-trash"></i>';
            deleteBtn.className = "text-red-500 hover:text-red-700 px-2";

            deleteBtn.addEventListener("click", () => {
                tasks.splice(index, 1);
                updateState();
            });

            li.appendChild(deleteBtn);
        }

        container.appendChild(li);
    });
}

function addTask() {
    const text = newTaskInput.value.trim();
    if (text) {
        tasks.push({ text: text, completed: false });
        newTaskInput.value = "";
        updateState();
    }
}

function updateState() {
    checklistInput.value = JSON.stringify(tasks);
    renderTasks();
}

document.addEventListener("DOMContentLoaded", function () {
    checklistInput = document.getElementById("checklist_data");
    container = document.getElementById("checklist-container");
    newTaskInput = document.getElementById("new-task-input");
    const addTaskBtn = document.querySelector('button[onclick="addTask()"]');
    const statusSelect = document.getElementById('status');

    if (!checklistInput || !container) return;

    try {
        tasks = JSON.parse(checklistInput.value || "[]");
    } catch (e) {
        console.error("Erro ao ler checklist:", e);
        tasks = [];
    }

    if (addTaskBtn) {
        addTaskBtn.addEventListener("click", addTask);
    }

    if (newTaskInput) {
        newTaskInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                addTask();
            }
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', renderTasks);
    }

    renderTasks();
});