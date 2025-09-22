export function useLoading() {
    const counter = useState<number>('__loading_counter', () => 0)
    const isLoading = computed(() => counter.value > 0)
    function start() { counter.value++ }
    function stop() { counter.value = Math.max(0, counter.value - 1) }
    return { isLoading, start, stop }
}
