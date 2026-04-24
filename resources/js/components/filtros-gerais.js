document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll(".dropdown-container");
    const filterForm = document.getElementById("filter-form");

    function enviarFiltrosGerais() {
        if (!filterForm) return;

        document.querySelectorAll(".hidden-filter-copy").forEach((el) => el.remove());

        const checkboxesMarcadas = document.querySelectorAll(
            '.dropdown-container input[type="checkbox"]:checked',
        );

        checkboxesMarcadas.forEach((cb) => {
            if (!filterForm.contains(cb)) {
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = cb.name;
                hiddenInput.value = cb.value;
                hiddenInput.className = "hidden-filter-copy";
                filterForm.appendChild(hiddenInput);
            }
        });

        filterForm.submit();
    }

    if (filterForm) {
        filterForm.addEventListener("submit", function (e) {
            e.preventDefault();
            enviarFiltrosGerais();
        });

        filterForm.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                if (e.target.classList.contains("internal-search-input"))
                    return;
                e.preventDefault();
                enviarFiltrosGerais();
            }
        });
    }

    document.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            if (e.target.classList.contains("internal-search-input")) return;

            const isInsideForm = filterForm && filterForm.contains(e.target);
            const isInsideDropdown = e.target.closest(".dropdown-container");

            if (isInsideForm || isInsideDropdown) {
                e.preventDefault();
                enviarFiltrosGerais();
            }
        }
    });

    // Fechar menus ao fazer scroll ou redimensionar 
    // O parâmetro 'true' no final captura o scroll até de dentro da tabela
    window.addEventListener(
        "scroll",
        function (e) {
            // Se estiver fazendo scroll DENTRO do dropdown não fecha
            if (e.target.closest && e.target.closest(".dropdown-menu")) return;

            dropdowns.forEach((container) => {
                const menu = container.querySelector(".dropdown-menu");
                if (menu && !menu.classList.contains("hidden")) {
                    menu.classList.add("hidden");
                    if (container.dataset.changed === "true")
                        enviarFiltrosGerais();
                }
            });
        },
        true,
    );

    window.addEventListener("resize", function () {
        dropdowns.forEach((container) => {
            const menu = container.querySelector(".dropdown-menu");
            if (menu && !menu.classList.contains("hidden")) {
                menu.classList.add("hidden");
                if (container.dataset.changed === "true") enviarFiltrosGerais();
            }
        });
    });

    dropdowns.forEach((container) => {
        const btn = container.querySelector(".dropdown-btn");
        const menu = container.querySelector(".dropdown-menu");
        const counterSpan = btn.querySelector("span");

        const btnAll = container.querySelector(".btn-select-all");
        const btnClear = container.querySelector(".btn-clear-all");

        const searchInput = container.querySelector(".internal-search-input");
        const listItems = container.querySelectorAll(".searchable-item");

        if (btn) {
            btn.addEventListener("click", (e) => {
                e.stopPropagation();

                const isOpening = menu.classList.contains("hidden");

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
                        if (other.dataset.changed === "true")
                            enviarFiltrosGerais();
                    }
                });

                if (isOpening) {
                    // Calcula onde o botão está na tela atualmente
                    const rect = btn.getBoundingClientRect();

                    // Mostra o menu para pegar a largura dele
                    menu.classList.remove("hidden");

                    // Transforma em modal (fixed) e define a altura
                    menu.style.position = "fixed";
                    menu.style.top = `${rect.bottom + 4}px`;
                    menu.style.zIndex = "99999";

                    // Evita que o menu passe da margem direita 
                    const menuWidth = menu.offsetWidth;
                    if (rect.left + menuWidth > window.innerWidth) {
                        menu.style.left = `${window.innerWidth - menuWidth - 15}px`;
                    } else {
                        menu.style.left = `${rect.left}px`;
                    }
                } else {
                    menu.classList.add("hidden");
                    if (container.dataset.changed === "true")
                        enviarFiltrosGerais();
                }
            });
        }

        const updateCounter = () => {
            const checkedCount = container.querySelectorAll(
                "input[type='checkbox']:checked",
            ).length;
            if (counterSpan) {
                counterSpan.innerText =
                    checkedCount > 0 ? checkedCount + " selecionados" : "Todos";
            }
        };

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

        const inputs = container.querySelectorAll("input");
        inputs.forEach((input) => {
            if (input.type === "text") return;

            input.addEventListener("change", () => {
                container.dataset.changed = "true";
                if (input.type === "radio") {
                    if (counterSpan)
                        counterSpan.innerText = input
                            .closest("label")
                            .querySelector("span").innerText;
                    menu.classList.add("hidden");
                    enviarFiltrosGerais();
                } else {
                    updateCounter();
                }
            });
        });

        if (searchInput && listItems.length > 0) {
            const emptyState = container.querySelector(".empty-state-msg");
            const termDisplay = container.querySelector(".search-term-display");

            searchInput.addEventListener("keydown", function (e) {
                if (e.key === "Enter") e.preventDefault();
            });

            searchInput.addEventListener("input", function (e) {
                const term = e.target.value.toLowerCase();
                let hasVisibleItems = false;

                listItems.forEach((item) => {
                    const name = item
                        .querySelector(".searchable-name")
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

    document.addEventListener("click", (e) => {
        dropdowns.forEach((container) => {
            const menu = container.querySelector(".dropdown-menu");
            if (
                menu &&
                !container.contains(e.target) &&
                !menu.classList.contains("hidden")
            ) {
                menu.classList.add("hidden");
                if (container.dataset.changed === "true") {
                    enviarFiltrosGerais();
                }
            }
        });
    });
});
