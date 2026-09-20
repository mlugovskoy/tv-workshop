export const repairStatusLabels: Record<string, string> = {
    NEW: 'Новый',
    DIAGNOSTICS: 'Диагностика',
    WAITING_APPROVAL: 'Ожидает согласования',
    WAITING_PART: 'Ожидает запчасть',
    IN_REPAIR: 'В ремонте',
    READY: 'Готов',
    ISSUED: 'Выдан',
    CANCELLED: 'Отменён',
};

export const repairStatusStyles: Record<string, string> = {
    NEW: 'bg-blue-100 text-blue-700',
    DIAGNOSTICS: 'bg-violet-100 text-violet-700',
    WAITING_APPROVAL: 'bg-amber-100 text-amber-700',
    WAITING_PART: 'bg-orange-100 text-orange-700',
    IN_REPAIR: 'bg-indigo-100 text-indigo-700',
    READY: 'bg-green-100 text-green-700',
    ISSUED: 'bg-slate-100 text-slate-600',
    CANCELLED: 'bg-red-100 text-red-700',
};
