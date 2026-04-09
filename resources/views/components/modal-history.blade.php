<div id="generalHistoryModal" class="fixed inset-0 z-[9999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">

  <div class="fixed inset-0 bg-gray-900/50 transition-opacity backdrop-blur-sm" onclick="closeGeneralHistoryModal()"></div>

  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4 text-center">

      <div class="relative w-full transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:max-w-4xl">

        <div class="bg-white p-4 lg:p-6 border-b border-gray-100 flex items-start justify-between gap-4">
          <div>
            <h3 class="text-lg sm:text-xl font-semibold leading-6 text-gray-900 flex items-center" id="modal-title">
              <i class="bi bi-clock-history text-blue-600 mr-2"></i> Histórico de Alterações
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-1" id="historySubtitle">Detalhes cronológicos das modificações.</p>
          </div>
          <button type="button" onclick="closeGeneralHistoryModal()" class="text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 border border-transparent hover:border-gray-200 rounded-full p-2 transition-all flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-blue-500" title="Fechar">
            <i class="bi bi-x-lg text-sm sm:text-base"></i>
          </button>
        </div>

        <div class="bg-gray-50 p-4 lg:p-6 min-h-[150px] max-h-[75vh] sm:max-h-[70vh] overflow-y-auto w-full flex flex-col" id="generalHistoryContent">
        </div>

        <div class="bg-white p-4 lg:p-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end">
          <button type="button" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-white px-6 py-2.5 sm:py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="closeGeneralHistoryModal()">
            Fechar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>