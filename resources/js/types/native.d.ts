interface NativeApi {
    on(
        event: string,
        callback: (payload: any, event: string) => void
    ): void;
}

declare global {
    interface Window {
        Native: NativeApi;
    }
}

export {};
