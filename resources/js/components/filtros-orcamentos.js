document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll(".dropdown-container");

    dropdowns.forEach((container) => {
        const btn = container.querySelector(".dropdown-btn");
        const menu = container.querySelector(".dropdown-menu");
        const counterSpan = btn.querySelector("span");
        const btnAll = container.querySelector(".btn-select-all");
        const btnClear = container.querySelector(".btn-clear-all");

        // Abrir/Fechar Dropdown
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdowns.forEach((other) => {
                if (other !== container)
                    other
                        .querySelector(".dropdown-menu")
                        .classList.add("hidden");
            });
            menu.classList.toggle("hidden");
        });

        // Função para atualizar o texto do contador
        const updateCounter = () => {
            const checkedCount = container.querySelectorAll(
                ".status-checkbox:checked",
            ).length;
            if (counterSpan)
                counterSpan.innerText = checkedCount + " selecionados";
        };

        // Atalho: Marcar Todos
        if (btnAll) {
            btnAll.addEventListener("click", (e) => {
                e.stopPropagation();
                container.querySelectorAll(".status-checkbox").forEach((cb) => {
                    cb.checked = true;
                    cb.dispatchEvent(new Event("change")); // Dispara a mudança visual
                });
                updateCounter();
            });
        }

        // Atalho: Limpar Tudo
        if (btnClear) {
            btnClear.addEventListener("click", (e) => {
                e.stopPropagation();
                container.querySelectorAll(".status-checkbox").forEach((cb) => {
                    cb.checked = false;
                    cb.dispatchEvent(new Event("change"));
                });
                updateCounter();
            });
        }

        // Mudança manual nos checkboxes ou radios
        const inputs = container.querySelectorAll("input");
        inputs.forEach((input) => {
            input.addEventListener("change", () => {
                if (input.type === "radio") {
                    counterSpan.innerText = input
                        .closest("label")
                        .querySelector("span").innerText;
                    menu.classList.add("hidden");
                } else {
                    updateCounter();
                }
            });
        });
    });

    // Fechar ao clicar fora
    document.addEventListener("click", (e) => {
        dropdowns.forEach((container) => {
            if (!container.contains(e.target)) {
                container
                    .querySelector(".dropdown-menu")
                    .classList.add("hidden");
            }
        });
    });
});
