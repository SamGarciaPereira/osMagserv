<div id="generalHistoryModal" class="fixed inset-0 z-[9999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeGeneralHistoryModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <div class="sm:flex sm:items-start justify-between">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-xl font-semibold leading-6 text-gray-900" id="modal-title">
                                <i class="bi bi-clock-history text-blue-600 mr-2"></i>Histórico de Alterações
                            </h3>
                            <p class="text-sm text-gray-500 mt-1" id="historySubtitle">Detalhes cronológicos das modificações.</p>
                        </div>
                        <button type="button" onclick="closeGeneralHistoryModal()" class="text-gray-400 hover:text-gray-600 transition">
                            <i class="bi bi-x-lg text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-6 sm:p-6 max-h-[70vh] overflow-y-auto" id="generalHistoryContent">
                    </div>

                <div class="bg-white px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeGeneralHistoryModal()">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>