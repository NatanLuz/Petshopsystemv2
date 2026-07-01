// funcao de confirmacao antes de excluir
function confirmDelete(
  message = "Tem certeza que deseja excluir este registro?"
) {
  return confirm(message);
}

// Mascara para CPF
function maskCPF(input) {
  let value = input.value.replace(/\D/g, "");
  value = value.replace(/(\d{3})(\d)/, "$1.$2");
  value = value.replace(/(\d{3})(\d)/, "$1.$2");
  value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
  input.value = value;
}

// Mascara para telefone
function maskPhone(input) {
  let value = input.value.replace(/\D/g, "");
  if (value.length <= 10) {
    value = value.replace(/(\d{2})(\d)/, "($1) $2");
    value = value.replace(/(\d{4})(\d)/, "$1-$2");
  } else {
    value = value.replace(/(\d{2})(\d)/, "($1) $2");
    value = value.replace(/(\d{5})(\d)/, "$1-$2");
  }
  input.value = value;
}

// Mascara para CEP
function maskCEP(input) {
  let value = input.value.replace(/\D/g, "");
  value = value.replace(/(\d{5})(\d)/, "$1-$2");
  input.value = value;
}

// Auto aplicar mascaras
document.addEventListener("DOMContentLoaded", function () {
  // para cpf
  const cpfInputs = document.querySelectorAll('input[name="cpf"]');
  cpfInputs.forEach((input) => {
    input.addEventListener("input", () => maskCPF(input));
  });

  //  para telefones
  const phoneInputs = document.querySelectorAll(
    'input[name="telefone"], input[name="celular"]'
  );
  phoneInputs.forEach((input) => {
    input.addEventListener("input", () => maskPhone(input));
  });

  // para cep
  const cepInputs = document.querySelectorAll('input[name="cep"]');
  cepInputs.forEach((input) => {
    input.addEventListener("input", () => maskCEP(input));
  });
});

// alerts apos 5 segundos
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });
});

// Busca em tempo real na tabela
function searchTable(input, tableId) {
  const filter = input.value.toLowerCase();
  const table = document.getElementById(tableId);
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const row = rows[i];
    const cells = row.getElementsByTagName("td");
    let found = false;

    for (let j = 0; j < cells.length; j++) {
      const cell = cells[j];
      if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
        break;
      }
    }

    row.style.display = found ? "" : "none";
  }
}

function toggleSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".sidebar-overlay");
  const toggle = document.getElementById("mobileMenuToggle");
  const isOpen = sidebar.classList.toggle("active");

  if (overlay) {
    overlay.classList.toggle("active", isOpen);
  }

  if (toggle) {
    toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    toggle.setAttribute("aria-label", isOpen ? "Fechar menu" : "Abrir menu");
  }
}

function closeSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".sidebar-overlay");
  const toggle = document.getElementById("mobileMenuToggle");

  if (sidebar) {
    sidebar.classList.remove("active");
  }

  if (overlay) {
    overlay.classList.remove("active");
  }

  if (toggle) {
    toggle.setAttribute("aria-expanded", "false");
    toggle.setAttribute("aria-label", "Abrir menu");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".sidebar-overlay");
  const toggle = document.getElementById("mobileMenuToggle");
  const menuLinks = document.querySelectorAll(".sidebar-menu a");

  if (!sidebar || !toggle) {
    return;
  }

  toggle.addEventListener("click", toggleSidebar);

  if (overlay) {
    overlay.addEventListener("click", closeSidebar);
  }

  menuLinks.forEach((link) => {
    link.addEventListener("click", function () {
      if (window.matchMedia("(max-width: 768px)").matches) {
        closeSidebar();
      }
    });
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeSidebar();
    }
  });

  window.addEventListener("resize", function () {
    if (!window.matchMedia("(max-width: 768px)").matches) {
      closeSidebar();
    }
  });
});
