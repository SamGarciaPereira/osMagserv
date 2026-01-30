const defaultLabels = {
    'created_at': 'Criado em',
    'updated_at': 'Atualizado em',
    'status': 'Status',
    'description': 'Descrição',
    'user_id': 'Usuário Responsável',
    'valor': 'Valor',
    'comentario': 'Comentários',
    'checklist': 'Checklist',
    'data_solicitacao': 'Data da Solicitação',
    'data_envio': 'Data de Envio',
    'data_aprovacao': 'Data de Aprovação',
    'escopo': 'Escopo / Demanda',
    'numero_proposta': 'Nº Proposta',
    'cliente_id': 'Cliente',
    'revisao': 'Revisão',
    'cep_obra': 'CEP Obra',
    'cidade_obra': 'Cidade Obra',
    'logradouro_obra': 'Logradouro',
    'bairro_obra': 'Bairro',
    'numero_obra': 'Número',
    'anexo': 'Arquivo Anexo'
};

const fieldIcons = {
    'valor': 'bi-currency-dollar',
    'status': 'bi-stoplights',
    'data': 'bi-calendar-event',
    'user': 'bi-person',
    'cliente': 'bi-building',
    'checklist': 'bi-list-check',
    'comentario': 'bi-chat-text',
    'escopo': 'bi-file-text',
    'revisao': 'bi-arrow-repeat',
    'anexo': 'bi-paperclip',
    'default': 'bi-pencil-square'
};

window.openGeneralHistoryModal = function(historyData, customFieldLabels = {}) {
    const modal = document.getElementById('generalHistoryModal');
    const contentDiv = document.getElementById('generalHistoryContent');
    contentDiv.innerHTML = '';

    const fieldLabels = { ...defaultLabels, ...customFieldLabels };

    if (!historyData || historyData.length === 0) {
        renderEmptyState(contentDiv);
    } else {
        historyData.forEach((activity, index) => {
            const dateObj = new Date(activity.created_at);
            const date = dateObj.toLocaleDateString('pt-BR'); 
            const time = dateObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            
            const userName = activity.user ? activity.user.name : 'Sistema';
            
            const timelineHtml = renderTimelineItem(activity, index, historyData.length, userName, date, time, fieldLabels);
            contentDiv.insertAdjacentHTML('beforeend', timelineHtml);
        });
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};

window.closeGeneralHistoryModal = function() {
    const modal = document.getElementById('generalHistoryModal');
    if(modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
};

function renderTimelineItem(activity, index, total, userName, date, time, labels) {
    const isLast = index === total - 1;
    let contentHtml = '';

    if (activity.event === 'created') {
        contentHtml = `
            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center text-green-800 shadow-sm">
                <div class="bg-green-100 p-2 rounded-full mr-3"><i class="bi bi-plus-lg text-lg"></i></div>
                <div>
                    <span class="font-bold block text-sm">Registro Criado</span>
                    <span class="text-xs opacity-75">O registro foi inserido no sistema pela primeira vez.</span>
                </div>
            </div>`;
    } 
    else if (activity.event === 'deleted') {
        contentHtml = `
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center text-red-800 shadow-sm">
                <div class="bg-red-100 p-2 rounded-full mr-3"><i class="bi bi-trash text-lg"></i></div>
                <div>
                    <span class="font-bold block text-sm">Registro Removido</span>
                    <span class="text-xs opacity-75">Este item foi excluído.</span>
                </div>
            </div>`;
    }
    else if (activity.event === 'updated' && activity.properties) {
        contentHtml = renderChangesGrid(activity.properties, labels);
    }

    return `
        <div class="relative pl-8 sm:pl-10 pb-8 ${isLast ? '' : 'border-l-2 border-gray-200'}">
            <div class="absolute left-[-9px] top-0 bg-white p-1 z-10">
                <div class="h-4 w-4 rounded-full ${activity.event === 'created' ? 'bg-green-500 shadow-green-200' : 'bg-blue-600 shadow-blue-200'} shadow-[0_0_0_4px] ring-1 ring-white"></div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 bg-gray-50/50 p-2 rounded-lg -ml-2">
                <div class="flex items-center gap-2">
                     <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border ${activity.event === 'created' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-blue-100 text-blue-700 border-blue-200'}">
                        v.${activity.version}
                    </span>
                    <span class="font-bold text-gray-800 text-sm flex items-center gap-1">
                        <i class="bi bi-person-circle text-gray-400"></i> ${userName}
                    </span>
                </div>
                <div class="text-xs text-gray-500 mt-1 sm:mt-0 flex items-center gap-3">
                    <span title="Data/Hora" class="font-medium bg-white px-2 py-0.5 rounded border border-gray-200 shadow-sm">
                        <i class="bi bi-calendar3 mr-1"></i> ${date} <span class="text-gray-300 mx-1">|</span> <i class="bi bi-clock mr-1"></i> ${time}
                    </span>
                </div>
            </div>
            
            <div class="text-sm">
                ${contentHtml}
            </div>
        </div>
    `;
}

function renderChangesGrid(properties, labels) {
    const attributes = properties.attributes || {};
    const old = properties.old || {};
    let cardsHtml = '';
    let hasChanges = false;

    for (const [key, newValue] of Object.entries(attributes)) {
        if (key === 'updated_at') continue;

        hasChanges = true;
        const label = labels[key] || key;
        
        let icon = fieldIcons['default'];
        if (key.includes('data')) icon = fieldIcons['data'];
        else if (key.includes('valor') || key.includes('preco')) icon = fieldIcons['valor'];
        else if (fieldIcons[key]) icon = fieldIcons[key];

        if (key === 'checklist') {
            const diffChecklist = getChecklistDiff(old[key], newValue);
            if (diffChecklist) {
                cardsHtml += `
                <div class="mb-4 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="bg-gray-100/50 px-4 py-2 border-b border-gray-200 flex items-center gap-2">
                        <i class="bi ${icon} text-blue-500"></i>
                        <span class="font-bold text-gray-700 text-xs uppercase tracking-wider">${label}</span>
                    </div>
                    <div class="p-4">
                        ${diffChecklist}
                    </div>
                </div>`;
                continue;
            }
        }

        if (key === 'anexo') {
            const isUpload = newValue !== null;
            const fileName = isUpload ? newValue : old[key];
            const actionColor = isUpload ? 'green' : 'red';
            const actionText = isUpload ? 'Adicionado' : 'Removido';
            const actionIcon = isUpload ? 'bi-cloud-upload' : 'bi-trash';

            cardsHtml += `
            <div class="mb-4 bg-white border border-${actionColor}-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="bg-${actionColor}-50 px-4 py-2 border-b border-${actionColor}-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-${actionColor}-700">
                        <i class="bi bi-paperclip"></i>
                        <span class="font-bold text-xs uppercase tracking-wider">Alteração de Anexo</span>
                    </div>
                    <span class="text-[10px] uppercase font-bold bg-white px-2 py-0.5 rounded text-${actionColor}-600 border border-${actionColor}-200">${actionText}</span>
                </div>
                <div class="p-4 flex items-center gap-3">
                    <div class="bg-${actionColor}-100 p-2 rounded-full text-${actionColor}-600">
                        <i class="bi ${actionIcon} text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">${fileName}</p>
                        <p class="text-xs text-gray-500">Arquivo ${isUpload ? 'anexado ao registro' : 'removido do registro'}.</p>
                    </div>
                </div>
            </div>`;
            continue;
        }

        const { displayOld, displayNew } = formatValues(key, old[key], newValue);

        cardsHtml += `
            <div class="mb-3 last:mb-0 group">
                <div class="flex items-center gap-2 mb-1.5 ml-1">
                    <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="bi ${icon}"></i>
                    </span>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">${label}</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-0 md:gap-4 items-stretch">
                    <div class="relative bg-red-50/60 border border-red-100 rounded-lg p-3 text-red-800 text-sm break-words flex flex-col justify-center min-h-[50px]">
                        <span class="absolute top-1 right-2 text-[10px] font-bold text-red-300 uppercase select-none">Antes</span>
                        <div class="whitespace-pre-wrap">${displayOld}</div>
                    </div>
                    <div class="flex items-center justify-center py-1 md:py-0 text-gray-300">
                        <i class="bi bi-arrow-down md:bi-arrow-right text-lg md:text-xl transform group-hover:text-blue-400 group-hover:scale-110 transition-all"></i>
                    </div>
                    <div class="relative bg-green-50/60 border border-green-100 rounded-lg p-3 text-green-800 text-sm break-words flex flex-col justify-center min-h-[50px]">
                        <span class="absolute top-1 right-2 text-[10px] font-bold text-green-300 uppercase select-none">Depois</span>
                        <div class="whitespace-pre-wrap font-medium">${displayNew}</div>
                    </div>
                </div>
            </div>
        `;
    }

    if (!hasChanges) {
        return '<div class="text-gray-400 italic text-xs py-2">Atualização interna do sistema (sem alterações visíveis).</div>';
    }

    return `<div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm space-y-4">${cardsHtml}</div>`;
}

function formatValues(key, valOld, valNew) {
    let displayOld = valOld;
    let displayNew = valNew;

    if (displayOld === null || displayOld === '' || displayOld === undefined) 
        displayOld = '<span class="text-gray-400 italic text-xs">Vazio</span>';
    
    if (displayNew === null || displayNew === '' || displayNew === undefined) 
        displayNew = '<span class="text-gray-400 italic text-xs">Vazio</span>';

    const dateRegex = /^(\d{4})-(\d{2})-(\d{2})/;
    
    if (typeof valNew === 'string') {
        const match = valNew.match(dateRegex);
        if (match) {
            displayNew = `${match[3]}/${match[2]}/${match[1]}`;
        }
    }
    
    if (typeof valOld === 'string') {
        const match = valOld.match(dateRegex);
        if (match) {
            displayOld = `${match[3]}/${match[2]}/${match[1]}`;
        }
    }

    if (key.toLowerCase().includes('valor') || key.toLowerCase().includes('preco')) {
        if (!isNaN(parseFloat(valOld))) displayOld = parseFloat(valOld).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        if (!isNaN(parseFloat(valNew))) displayNew = parseFloat(valNew).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    if (typeof valNew === 'object' && valNew !== null) displayNew = '<span class="text-xs bg-gray-200 px-1 rounded">Objeto/Dados</span>';
    if (typeof valOld === 'object' && valOld !== null) displayOld = '<span class="text-xs bg-gray-200 px-1 rounded">Objeto/Dados</span>';

    return { displayOld, displayNew };
}

function getChecklistDiff(oldJson, newJson) {
    const oldList = (typeof oldJson === 'string' ? JSON.parse(oldJson) : oldJson) || [];
    const newList = (typeof newJson === 'string' ? JSON.parse(newJson) : newJson) || [];
    let diffs = [];
    const oldMap = {};
    oldList.forEach(item => oldMap[item.text] = item.completed);

    newList.forEach(item => {
        const oldStatus = oldMap[item.text];
        const newStatus = item.completed;

        if (oldStatus !== undefined && oldStatus !== newStatus) {
            const statusIcon = newStatus 
                ? '<span class="text-green-600 font-bold text-xs flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> Concluído</span>' 
                : '<span class="text-orange-500 font-bold text-xs flex items-center gap-1"><i class="bi bi-circle"></i> Reaberto</span>';
            
            diffs.push(`
                <div class="flex items-center justify-between p-2 bg-white border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                    <span class="text-gray-700 text-sm">${item.text}</span>
                    <div class="ml-4">${statusIcon}</div>
                </div>
            `);
        } else if (oldStatus === undefined) {
             diffs.push(`
                <div class="flex items-center justify-between p-2 bg-blue-50/30 border-l-2 border-blue-400 mb-1 rounded-r">
                    <span class="text-blue-800 text-sm"><i class="bi bi-plus-circle mr-1"></i> Nova tarefa: <strong>${item.text}</strong></span>
                </div>
            `);
        }
    });

    return diffs.length > 0 ? diffs.join('') : null;
}

function renderEmptyState(container) {
    container.innerHTML = `
        <div class="flex flex-col items-center justify-center py-16 opacity-50">
            <div class="bg-gray-100 p-4 rounded-full mb-3">
                <i class="bi bi-clock-history text-4xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 font-medium">Nenhum histórico encontrado.</p>
        </div>`;
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") window.closeGeneralHistoryModal();
});