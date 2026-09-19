export const formatPrice = (price: number | string | null): string => {
    if (price === null) {
        return '—';
    }

    return `${Number(price).toLocaleString('ru-RU')} ₽`;
};
