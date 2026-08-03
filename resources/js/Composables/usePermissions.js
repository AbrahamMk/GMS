import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();

    const permissions = computed(() => page.props.auth?.permissions ?? []);
    const roles = computed(() => page.props.auth?.roles ?? []);

    function can(permission) {
        if (!permission) {
            return true;
        }

        const list = permissions.value;

        if (Array.isArray(permission)) {
            return permission.some((item) => list.includes(item));
        }

        return list.includes(permission);
    }

    function canAny(items = []) {
        return items.some((item) => can(item));
    }

    function canAll(items = []) {
        return items.every((item) => can(item));
    }

    function hasRole(role) {
        return roles.value.includes(role);
    }

    return {
        permissions,
        roles,
        can,
        canAny,
        canAll,
        hasRole,
    };
}
