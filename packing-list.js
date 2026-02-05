document.addEventListener("DOMContentLoaded", () => {
    const items = Array.from(document.querySelectorAll(".packing-item"));
    const filterButtons = Array.from(
        document.querySelectorAll(".packing-filter-btn")
    );

    const modal = document.getElementById("addItemModal");
    const openModalBtn = document.getElementById("openAddItemModal");
    const closeModalBtn = document.getElementById("closeAddItemModal");
    const cancelAddItemBtn = document.getElementById("cancelAddItem");
    const form = modal ? modal.querySelector("form") : null;

    items.forEach((item) => {
        const checkBtn = item.querySelector(".packing-check");
        const statusChip = item.querySelector(".packing-status");

        if (!checkBtn || !statusChip) return;

        checkBtn.addEventListener("click", () => {
            const isPacked = item.classList.toggle("packing-item--packed");
            item.dataset.status = isPacked ? "packed" : "not-packed";
            checkBtn.classList.toggle("packing-check--checked", isPacked);

            if (isPacked) {
                statusChip.textContent = "Packed";
                statusChip.classList.remove("packing-status--not-packed");
                statusChip.classList.add("packing-status--packed");
            } else {
                statusChip.textContent = "Not packed";
                statusChip.classList.remove("packing-status--packed");
                statusChip.classList.add("packing-status--not-packed");
            }

            applyCurrentFilter();
        });
    });

    /* Filter buttons */
    function applyCurrentFilter() {
        const active = document.querySelector(".packing-filter-btn--active");
        const filter = active ? active.dataset.filter : "all";

        items.forEach((item) => {
            const status = item.dataset.status || "not-packed";

            if (filter === "all") {
                item.style.display = "";
            } else if (filter === "packed") {
                item.style.display = status === "packed" ? "" : "none";
            } else if (filter === "not-packed") {
                item.style.display = status === "not-packed" ? "" : "none";
            }
        });
    }

    filterButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            filterButtons.forEach((b) =>
                b.classList.remove("packing-filter-btn--active")
            );
            btn.classList.add("packing-filter-btn--active");
            applyCurrentFilter();
        });
    });

    function openModal() {
        if (!modal) return;
        modal.classList.add("is-visible");
        modal.setAttribute("aria-hidden", "false");
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove("is-visible");
        modal.setAttribute("aria-hidden", "true");
    }

    if (openModalBtn) openModalBtn.addEventListener("click", openModal);
    if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);
    if (cancelAddItemBtn) {
        cancelAddItemBtn.addEventListener("click", (e) => {
            e.preventDefault();
            closeModal();
        });
    }

    if (form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            closeModal();
        });
    }

    applyCurrentFilter();
});
