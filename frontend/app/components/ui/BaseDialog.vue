<template>
    <Dialog
        v-model:visible="isVisible"
        :header="header"
        :footer="footer"
        :modal="modal"
        :position="position"
        :draggable="draggable"
        close-on-escape
        :pt="dialogPtOptions"
    >
        <slot />
        <template #footer>
            <slot name="footer">
                <div v-if="footer" class="w-full text-right">
                    {{ footer }}
                </div>
            </slot>
        </template>
    </Dialog>
</template>
<script lang="ts">
export default defineNuxtComponent({
    name: "BaseDialog",
    props: {
        size: {
            type: String,
            default: 'small',
            validator: value => ['small', 'medium', 'large'].includes(value)
        },
        header: String,
        footer: String,
        modal: {
            type: Boolean,
            default: false
        },
        draggable: {
            type: Boolean,
            default: false
        },
        position: {
            type: String,
            default: 'center'
        },
        visible: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:visible'],
    computed: {
        isVisible: {
            get() {
                return this.visible;
            },
            set(val) {
                this.$emit('update:visible', val);
            }
        },
        dialogPtOptions() {
            const sizeClasses = {
                small: 'w-64',
                medium: 'w-96 h-96',
                large: 'w-[40rem] h-[50rem]'
            };

            return {
                root: {
                    class: sizeClasses[this.size] || sizeClasses.small
                }
            };
        }
    }
});
</script>