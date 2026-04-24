document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll(".dropdown-container");
    const filterForm = document.getElementById("filter-form");

    dropdowns.forEach((container) => {
        const btn = container.querySelector(".dropdown-btn");
        const menu = container.querySelector(".dropdown-menu");
        const counterSpan = btn.querySelector("span");

        const btnAll = container.querySelector(".btn-select-all");
        const btnClear = container.querySelector(".btn-clear-all");

        const searchInput = container.querySelector(".client-search-input");
        const clientItems = container.querySelectorAll(".client-item");

        // Abrir/Fechar Dropdown
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdowns.forEach((other) => {
                if (
                    other !== container &&
                    !other
                        .querySelector(".dropdown-menu")
                        .classList.contains("hidden")
                ) {
                    other
                        .querySelector(".dropdown-menu")
                        .classList.add("hidden");

                    if (other.dataset.changed === "true" && filterForm) {
                        filterForm.submit();
                    }
                }
            });
            menu.classList.toggle("hidden");
        });

        // Função para atualizar o texto do contador
        const updateCounter = () => {
            const checkedCount = container.querySelectorAll(
                ".status-checkbox:checked",
            ).length;
            if (counterSpan) {
                counterSpan.innerText =
                    checkedCount > 0 ? checkedCount + " selecionados" : "Todos";
            }
        };

        // Atalho: Marcar Todos
        if (btnAll) {
            btnAll.addEventListener("click", (e) => {
                e.stopPropagation();
                container.dataset.changed = "true";
                container.querySelectorAll(".status-checkbox").forEach((cb) => {
                    cb.checked = true;
                    cb.dispatchEvent(new Event("change"));
                });
                updateCounter();
            });
        }

        // Atalho: Limpar Tudo
        if (btnClear) {
            btnClear.addEventListener("click", (e) => {
                e.stopPropagation();
                container.dataset.changed = "true";
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
            // Ignora o input da pesquisa para não dar trigger de submit
            if (input.type === "text") return;

            input.addEventListener("change", () => {
                container.dataset.changed = "true"; // Registra que houve mudança

                if (input.type === "radio") {
                    if (counterSpan)
                        counterSpan.innerText = input
                            .closest("label")
                            .querySelector("span").innerText;
                    menu.classList.add("hidden");
                    if (filterForm) filterForm.submit(); 
                } else {
                    updateCounter();
                }
            });
        });

        // Barra de pesquisa interna
        if (searchInput && clientItems.length > 0) {
            const emptyState = container.querySelector(".client-empty-state");
            const termDisplay = container.querySelector(".search-term-display");

            searchInput.addEventListener("keydown", function (e) {
                if (e.key === "Enter") e.preventDefault();
            });

            searchInput.addEventListener("input", function (e) {
                const term = e.target.value.toLowerCase();
                let hasVisibleItems = false; // Variável para rastrear se achou alguém

                clientItems.forEach((item) => {
                    const name = item
                        .querySelector(".client-name")
                        .textContent.toLowerCase();

                    if (name.includes(term)) {
                        item.style.display = "flex";
                        hasVisibleItems = true; // Achou pelo menos um
                    } else {
                        item.style.display = "none";
                    }
                });

                // Lógica para mostrar/esconder a mensagem de "Nenhum cliente"
                if (emptyState) {
                    if (hasVisibleItems) {
                        emptyState.classList.add("hidden");
                    } else {
                        emptyState.classList.remove("hidden");
                        if (termDisplay)
                            termDisplay.textContent = e.target.value; // Mostra o termo digitado
                    }
                }
            });
        }
    });

    // Fechar ao clicar fora e Auto-Submit Geral
    document.addEventListener("click", (e) => {
        dropdowns.forEach((container) => {
            const menu = container.querySelector(".dropdown-menu");
            // Se o menu não contiver o clique e estiver aberto
            if (
                !container.contains(e.target) &&
                !menu.classList.contains("hidden")
            ) {
                menu.classList.add("hidden");

                //Se clicou fora e tem mudanças, submete
                if (container.dataset.changed === "true" && filterForm) {
                    filterForm.submit();
                }
            }
        });
    });
});
