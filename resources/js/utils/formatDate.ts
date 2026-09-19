export const formatDate = (date: string | null): string => {
    if (!date) {
        return "—";
    }

    return new Date(date).toLocaleString("ru-RU", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

export const formatDateList = (date: string | null): string => {
    if (!date) {
        return "—";
    }

    return new Date(date).toLocaleString("ru-RU", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};
