<template>
    <DataTable
        v-model:editingRows="internalEditingRows"
        @row-edit-init="$emit('row-edit-init', $event)"
        @row-edit-save="$emit('row-edit-save', $event)"
        @row-edit-cancel="$emit('row-edit-cancel', $event)"
        @page="$emit('page', $event)"
        :value="tableValue"
        :editMode="editMode"
        selectionMode="single"
        :paginator="paginator"
        :rows="rows"
        :total-records="totalRecords"
        :rows-per-page-options="rowsPerPageOptions"
        :paginator-template="paginatorTemplate"
        :lazy="lazy"
        :loading="loading"
        :dataKey="dataKey"
        :striped-rows="stripedRows"
        :size="size"
        :scrollable="scrollable"
        :pt="pt"
    >
        <template #header>
            <slot name="header">
                <div class="flex items-center justify-between">
                    <span class="text-lg font-bold">{{ header }}</span>
                </div>
            </slot>
        </template>
        <slot />
        <template #empty>
            <slot name="empty">
                <div class="p-4 text-center text-gray-400 italic">
                    No Data
                </div>
            </slot>
        </template>
    </DataTable>
</template>
<script lang="ts">
export default defineNuxtComponent({
    name: "BaseDataTable",
    props: {
        header: String,
        tableValue: {
            type: Array as PropType<any[]>,
            required: true
        },
        editingRows: {
            type: Object as PropType<Record<string, any>>,
            default: () => ({})
        },
        editMode: {
            type: String,
            default: 'row'
        },
        paginator: {
            type: Boolean,
            default: false
        },
        rows: {
            type: Number,
            default: 10,
        },
        totalRecords: {
            type: Number,
            default: 0,
        },
        rowsPerPageOptions: {
            type: Array as PropType<number[]>,
            default: () => [10, 20, 50, 100]
        },
        paginatorTemplate: {
            type: String,
            default: 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown'
        },
        lazy: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        dataKey: {
            type: String,
            default: 'id',
        },
        stripedRows: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
            default: 'small'
        },
        scrollable: {
            type: Boolean,
            default: true
        },
        pt: {
            type: Object,
            default: () => ({})
        }
    },
    emits: ['update:editing-rows', 'row-edit-init', 'row-edit-save', 'row-edit-cancel', 'page'],
    computed: {
        internalEditingRows: {
            get(this: any) {
                return this.editingRows;
            },
            set(this: any, val: Record<string, any>) {
                this.$emit("update:editing-rows", val);
            }
        }
    },
});
</script>

<style>
.p-paginator {
    border-radius: 0 !important;
}
.p-datatable .p-datatable-row-editor-init .p-button-icon {
    padding: 0.1rem;
}
.p-datatable .p-datatable-row-editor-save .p-button-icon {
    padding: 0.1rem;
}
.p-datatable .p-datatable-row-editor-cancel .p-button-icon {
    padding: 0.1rem;
}
.p-datatable .p-datatable-row-editor-init {
    padding: 0;
    min-width: unset;
}
.p-datatable .p-datatable-row-editor-save {
    padding: 0;
    min-width: unset;
}
.p-datatable .p-datatable-row-editor-cancel {
    padding: 0;
    min-width: unset;
}
</style>