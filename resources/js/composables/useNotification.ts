import {ref} from "vue";

const message = ref('');
const visible = ref(false);
const type = ref<'success' | 'error'>('success');

const showSuccess = (text: string) => {
    message.value = text;
    type.value = 'success';
    visible.value = true;

    setTimeout(() => {
        visible.value = false;
    }, 3000)
}

const showError = (text: string) => {
    message.value = text;
    type.value = 'error';
    visible.value = true;

    setTimeout(() => {
        visible.value = false;
    }, 3000);
};

export const useNotification = () => {
    return {
        message,
        visible,
        type,
        showSuccess,
        showError,
    };
};
