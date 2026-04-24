document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll(".dropdown-container");
    const filterForm = document.getElementById("filter-form");

    // captura as opções da tabela e injeta no formulário principal
    function enviarFiltrosComClientes() {
        if (!filterForm) return;

        // Limpa cópias anteriores para não duplicar na URL
        document
            .querySelectorAll(".hidden-cliente-copy")
            .forEach((el) => el.remove());

        // Pega todos os clientes marcados na tabela 
        const clientesMarcados = document.querySelectorAll(
            'input[name="cliente_id[]"]:checked',
        );

        // Cria inputs escondidos dentro do formulário principal
        clientesMarcados.forEach((cb) => {
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "cliente_id[]";
            hiddenInput.value = cb.value;
            hiddenInput.className = "hidden-cliente-copy";
            filterForm.appendChild(hiddenInput);
        });

        filterForm.submit();
    }

    // Intercepta o clique manual no botão azul de Filtrar
    if (filterForm) {
        filterForm.addEventListener("submit", function (e) {
            e.preventDefault();
            enviarFiltrosComClientes();
        });
    }

    // Atalho Enter para submeter os filtros
    document.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            // Proteger a pesquisa interna de clientes
            if (e.target.classList.contains("client-search-input")) {
                return; 
            }

            // verifica se o cursor está dentro de qualquer área de filtro
            const isInsideForm = filterForm && filterForm.contains(e.target);
            const isInsideDropdown = e.target.closest(".dropdown-container");

            // Se for um Enter válido num campo de filtro, submete tudo
            if (isInsideForm || isInsideDropdown) {
                e.preventDefault();
                enviarFiltrosComClientes();
            }
        }
    });

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

                    if (other.dataset.changed === "true") {
                        enviarFiltrosComClientes();
                    }
                }
            });
            menu.classList.toggle("hidden");
        });

        // Função para atualizar o texto do contador
        const updateCounter = () => {
            const checkedCount = container.querySelectorAll(
                "input[type='checkbox']:checked",
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
                container
                    .querySelectorAll("input[type='checkbox']")
                    .forEach((cb) => {
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
                container
                    .querySelectorAll("input[type='checkbox']")
                    .forEach((cb) => {
                        cb.checked = false;
                        cb.dispatchEvent(new Event("change"));
                    });
                updateCounter();
            });
        }

        // Mudança manual nos inputs
        const inputs = container.querySelectorAll("input");
        inputs.forEach((input) => {
            if (input.type === "text") return; // Ignora a barra de pesquisa

            input.addEventListener("change", () => {
                container.dataset.changed = "true";

                if (input.type === "radio") {
                    if (counterSpan)
                        counterSpan.innerText = input
                            .closest("label")
                            .querySelector("span").innerText;
                    menu.classList.add("hidden");
                    enviarFiltrosComClientes(); // Rádio submete na hora
                } else {
                    updateCounter();
                }
            });
        });

        // Lógica barra de pesquisa interna
        if (searchInput && clientItems.length > 0) {
            const emptyState = container.querySelector(".client-empty-state");
            const termDisplay = container.querySelector(".search-term-display");

            searchInput.addEventListener("keydown", function (e) {
                if (e.key === "Enter") e.preventDefault();
            });

            searchInput.addEventListener("input", function (e) {
                const term = e.target.value.toLowerCase();
                let hasVisibleItems = false;

                clientItems.forEach((item) => {
                    const name = item
                        .querySelector(".client-name")
                        .textContent.toLowerCase();
                    if (name.includes(term)) {
                        item.style.display = "flex";
                        hasVisibleItems = true;
                    } else {
                        item.style.display = "none";
                    }
                });

                if (emptyState) {
                    if (hasVisibleItems) {
                        emptyState.classList.add("hidden");
                    } else {
                        emptyState.classList.remove("hidden");
                        if (termDisplay)
                            termDisplay.textContent = e.target.value;
                    }
                }
            });
        }
    });

    // Fechar ao clicar fora e Auto-Submit Geral
    document.addEventListener("click", (e) => {
        dropdowns.forEach((container) => {
            const menu = container.querySelector(".dropdown-menu");
            if (
                !container.contains(e.target) &&
                !menu.classList.contains("hidden")
            ) {
                menu.classList.add("hidden");

                if (container.dataset.changed === "true") {
                    enviarFiltrosComClientes();
                }
            }
        });
    });
});
