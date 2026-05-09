// =====================================================
// MAIN.JS — Scripts globais do sistema Spa Capilar
// =====================================================

// =====================================================
// AUTO-FECHAR ALERTAS
// Remove a flash message após 4 segundos
// =====================================================
document.addEventListener("DOMContentLoaded", function () {
  const alertas = document.querySelectorAll(".alerta");
  alertas.forEach(function (alerta) {
    setTimeout(function () {
      alerta.style.transition = "opacity 0.5s ease";
      alerta.style.opacity = "0";
      setTimeout(function () {
        alerta.remove();
      }, 500);
    }, 4000); // 4 segundos
  });

  // =====================================================
  // MENU RESPONSIVO — abre/fecha sidebar no mobile
  // =====================================================
  const btnMenu = document.getElementById("btn-menu");
  const sidebar = document.getElementById("sidebar");

  if (btnMenu && sidebar) {
    btnMenu.addEventListener("click", function () {
      sidebar.classList.toggle("aberto");
    });
  }

  // =====================================================
  // CONFIRMAÇÃO DE EXCLUSÃO
  // Adiciona confirmação em todos os links de excluir
  // =====================================================
  const botoesExcluir = document.querySelectorAll(".btn-excluir");
  botoesExcluir.forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      if (!confirm("Tem certeza que deseja excluir este registro?")) {
        e.preventDefault();
      }
    });
  });

  // =====================================================
  // MÁSCARA DE TELEFONE
  // Formata o campo telefone automaticamente
  // =====================================================
  const campoTelefone = document.getElementById("telefone");
  if (campoTelefone) {
    campoTelefone.addEventListener("input", function () {
      let valor = this.value.replace(/\D/g, ""); // só números
      if (valor.length <= 11) {
        valor = valor.replace(/^(\d{2})(\d)/g, "($1) $2");
        valor = valor.replace(/(\d)(\d{4})$/, "$1-$2");
      }
      this.value = valor;
    });
  }

  // =====================================================
  // CAMPO DE BUSCA — busca em tempo real na tabela
  // =====================================================
  const campoBusca = document.getElementById("busca-tabela");
  if (campoBusca) {
    campoBusca.addEventListener("keyup", function () {
      const termo = this.value.toLowerCase();
      const linhas = document.querySelectorAll("table tbody tr");

      linhas.forEach(function (linha) {
        const texto = linha.textContent.toLowerCase();
        linha.style.display = texto.includes(termo) ? "" : "none";
      });
    });
  }
});

// =====================================================
// FUNÇÃO GLOBAL — formata moeda brasileira
// Uso: formatarMoeda(150.00) → "R$ 150,00"
// =====================================================
function formatarMoeda(valor) {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(valor);
}

// =====================================================
// FUNÇÃO GLOBAL — formata data brasileira
// Uso: formatarData('2024-06-15') → "15/06/2024"
// =====================================================
function formatarData(dataISO) {
  if (!dataISO) return "-";
  const partes = dataISO.split("-");
  return partes[2] + "/" + partes[1] + "/" + partes[0];
}
