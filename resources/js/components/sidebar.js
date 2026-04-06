document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("sidebar-toggle");
    const sidebarTexts = document.querySelectorAll(".sidebar-text");

    // Elementos do Header da Sidebar
    const sidebarHeader = document.getElementById("sidebar-header");
    const sidebarBranding = document.getElementById("sidebar-branding");

    // Elementos Mobile
    const backdrop = document.getElementById("sidebar-backdrop");
    const btnOpenMobile = document.getElementById("mobile-menu-btn");
    const btnCloseMobile = document.getElementById("mobile-close-btn");

    // Dropdowns
    const dropdownBtnManutencao = document.getElementById(
        "dropdown-btn-manutencao",
    );
    const submenuManutencao = document.getElementById("submenu-manutencao");
    const arrowManutencao = document.getElementById("arrow-manutencao");

    const dropdownBtnFinanceiro = document.getElementById(
        "dropdown-btn-financeiro",
    );
    const submenuFinanceiro = document.getElementById("submenu-financeiro");
    const arrowFinanceiro = document.getElementById("arrow-financeiro");

    const dropdownBtnRH = document.getElementById("dropdown-btn-rh");
    const submenuRH = document.getElementById("submenu-rh");
    const arrowRH = document.getElementById("arrow-rh");

    
    // 2. LÓGICA MOBILE (Off-Canvas)
    function toggleMobileMenu() {
        if (sidebar) sidebar.classList.toggle("-translate-x-full");
        if (backdrop) backdrop.classList.toggle("hidden");
    }

    if (btnOpenMobile)
        btnOpenMobile.addEventListener("click", toggleMobileMenu);
    if (btnCloseMobile)
        btnCloseMobile.addEventListener("click", toggleMobileMenu);
    if (backdrop) backdrop.addEventListener("click", toggleMobileMenu);


    // 3. LÓGICA DESKTOP (Expandir/Recolher) 
    let isExpanded = false;

    // Ajuste inicial dos links dos submenus
    const allSubLinks = document.querySelectorAll(
        "#submenu-manutencao a, #submenu-financeiro a, #submenu-rh a",
    );
    allSubLinks.forEach((link) => {
        link.style.transition = "all 0.3s ease-in-out";
    });

    function setExpanded(state) {
        if (!sidebar) return;

        // Evita que a lógica de expansão do PC bagunce o layout do Mobile
        if (window.innerWidth < 768) return;

        isExpanded = state;
        if (isExpanded) {
            // --- ABRIR SIDEBAR (DESKTOP) ---
            sidebar.classList.remove("md:w-20");
            sidebar.classList.add("md:w-72");

            if (sidebarHeader) {
                sidebarHeader.classList.remove("md:justify-center");
                sidebarHeader.classList.add("md:justify-between");
            }
            if (sidebarBranding) {
                sidebarBranding.classList.remove("hidden");
                setTimeout(() => {
                    sidebarBranding.classList.remove("md:w-0", "md:opacity-0");
                    sidebarBranding.classList.add(
                        "md:w-auto",
                        "md:opacity-100",
                    );
                }, 50);
            }

            setTimeout(() => {
                sidebarTexts.forEach((text) => {
                    if (
                        !text.closest(
                            "#submenu-manutencao, #submenu-financeiro, #submenu-rh",
                        )
                    ) {
                        text.classList.remove("md:w-0", "md:opacity-0");
                        text.classList.add("md:w-full", "md:opacity-100");
                    }
                });
            }, 100);
        } else {
            // --- RECOLHER SIDEBAR (DESKTOP) ---
            sidebar.classList.add("md:w-20");
            sidebar.classList.remove("md:w-72");

            if (sidebarBranding) {
                sidebarBranding.classList.remove("md:w-auto", "md:opacity-100");
                sidebarBranding.classList.add("md:w-0", "md:opacity-0");
                setTimeout(() => {
                    sidebarBranding.classList.add("hidden");
                    if (sidebarHeader) {
                        sidebarHeader.classList.remove("md:justify-between");
                        sidebarHeader.classList.add("md:justify-center");
                    }
                }, 200);
            } else if (sidebarHeader) {
                sidebarHeader.classList.remove("md:justify-between");
                sidebarHeader.classList.add("md:justify-center");
            }

            sidebarTexts.forEach((text) => {
                text.classList.add("md:w-0", "md:opacity-0");
                text.classList.remove("md:w-full", "md:opacity-100");
            });

            // Fecha os submenus ao recolher a barra
            closeDropdown(submenuFinanceiro, arrowFinanceiro);
            closeDropdown(submenuManutencao, arrowManutencao);
            closeDropdown(submenuRH, arrowRH);
        }
    }

    // Botão de expandir do Desktop
    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => setExpanded(!isExpanded));
    }

    // 4. LÓGICA DOS DROPDOWNS (Submenus)
    function closeDropdown(submenu, arrow) {
        if (!submenu) return;
        if (!submenu.classList.contains("hidden")) {
            submenu.classList.add("hidden");
            if (arrow) arrow.classList.remove("rotate-180");
        }
    }

    function toggleDropdown(submenu, arrow) {
        if (!submenu) return;

        const isHidden = submenu.classList.contains("hidden");
        const linksTexts = submenu.querySelectorAll(".sidebar-text");

        if (isHidden) {
            // --- ABRIR SUBMENU ---
            submenu.classList.remove("hidden");
            if (arrow) arrow.classList.add("rotate-180");

            setTimeout(() => {
                linksTexts.forEach((text) => {
                    text.classList.remove(
                        "md:w-0",
                        "md:opacity-0",
                        "opacity-0",
                    );
                    text.classList.add(
                        "md:w-full",
                        "md:opacity-100",
                        "opacity-100",
                    );
                });
            }, 100);
        } else {
            // --- FECHAR SUBMENU ---
            linksTexts.forEach((text) => {
                text.classList.add("md:w-0", "md:opacity-0", "opacity-0");
                text.classList.remove(
                    "md:w-full",
                    "md:opacity-100",
                    "opacity-100",
                );
            });

            if (arrow) arrow.classList.remove("rotate-180");

            setTimeout(() => {
                submenu.classList.add("hidden");
            }, 300);
        }
    }

    // Centraliza o comportamento ao clicar num submenu
    function handleDropdownClick(submenu, arrow) {
        // Se estiver no mobile, apenas abre o dropdown
        if (window.innerWidth < 768) {
            toggleDropdown(submenu, arrow);
        } else {
            // Se estiver no PC e a sidebar estiver recolhida, expande ela primeiro
            if (!isExpanded) {
                setExpanded(true);
                setTimeout(() => toggleDropdown(submenu, arrow), 300);
            } else {
                toggleDropdown(submenu, arrow);
            }
        }
    }

    // Eventos dos botões de dropdown
    if (dropdownBtnFinanceiro)
        dropdownBtnFinanceiro.addEventListener("click", () =>
            handleDropdownClick(submenuFinanceiro, arrowFinanceiro),
        );
    if (dropdownBtnManutencao)
        dropdownBtnManutencao.addEventListener("click", () =>
            handleDropdownClick(submenuManutencao, arrowManutencao),
        );
    if (dropdownBtnRH)
        dropdownBtnRH.addEventListener("click", () =>
            handleDropdownClick(submenuRH, arrowRH),
        );
});
