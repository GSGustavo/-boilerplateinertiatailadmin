<script>

import { ref, reactive, onMounted } from 'vue'
import { Link, router, Head } from '@inertiajs/vue3';
import MainAdmin from '@/Components/layout/MainAdmin.vue';
  
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import { FilterMatchMode } from '@primevue/core/api';
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod'

import { useToast } from 'primevue/usetoast';

export default {
    layout: MainAdmin,
    components: {
        Head, PageBreadcrumb
    },
    setup() {
        const toast = useToast();
        const currentPageTitle = ref('Nome da Página')
        const loading = ref(false);

        // Rota para Salvar o item da página
        const routeToSave = ref(route(""));

        // Rota para Get dos itens da página
        const routeToGet = ref(route(""));

        // Outras requisições para buscar outros itens
        // const routeToGetIcons = ref(route("name.route"));

        // const getOtherItems = () => {


        //     const itemToPost = {};

        //     axios.post(routeToGetIcons.value, { ...itemToPost })
        //         .then((response) => {

        //             if (!response.data.hasErrors) {
        //                 icons.value = response.data.items;
        //                 loading.value = false;
        //                 toast.add({ severity: "success", summary: "Sucesso!", detail: "Itens recarregados.", life: 5000 })
        //             } else {
        //                 toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
        //             }

        //         }).catch(function (error) {
        //             toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
        //         })
        // }

        const getItems = () => {
            loading.value = true;

            const itemToPost = {};

            axios.post(routeToGet.value, { ...itemToPost })
                .then((response) => {

                    if (!response.data.hasErrors) {
                        items.value = response.data.items;
                        loading.value = false;
                        toast.add({ severity: "success", summary: "Sucesso!", detail: "Itens recarregados.", life: 5000 })
                    } else {
                        toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                    }

                }).catch(function (error) {
                    toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                })
        }

        onMounted(() => {
            getItems();
            // getOtherItems();
        });

        const itemDialog = ref(false);

        const selectedItems = ref();

        const filters = ref({
            'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
        });

        const items = ref([]);
   

        const icons = ref([]);

        const item = ref({
            id: null,
            status: true
        });

        const hideDialog = () => {
            itemDialog.value = false;

            item.value = {
                id: null, status: true
            };
          
        };

        const editItem = (data, index) => {
            item.value = { ...data };

            item.value.index = index

            itemDialog.value = true;
        };

        const saveItem = ({ valid }) => {

            const itemToPost = { ...item.value }

            if (valid) {
                axios.post(routeToSave.value, { ...itemToPost, operation: 1 })
                    .then((response) => {

                        const data = response.data;

                        if (!data.hasErrors) {

                            item.value.created_at = data.item.created_at;
                            item.value.updated_at = data.item.updated_at;

                            if (item.value.id) {
                                items.value[item.value.index] = item.value;
                            }
                            else {
                                item.value.id = data.item.id
                                items.value.push(item.value);
                            }

                            let count = 0;

                            data.alerts.forEach((el) => {
                                toast.add({ severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count });

                                count += 1000
                            })

                            hideDialog();

                        }
                    }).catch(function (error) {
                        toast.add({ severity: "error", summary: "Erro!", detail: error, life: 5000 })
                    })
            }


        };

        const getStatusLabel = (status) => {
            switch (status) {
                case true:
                    return 'success';
                default:
                    return 'danger'
            }
        };

        const openNew = () => {
            item.value.new = true;
            itemDialog.value = true;
        }


        const resolver = ref(zodResolver(
            z.object({
  
                nome: z.string({ required_error: 'O nome é obrigatório!', invalid_type_error: "O nome é obrigatório", }).min(1, { message: 'O nome é obrigatório' }),
             
            })
        ))

        return {
            resolver,
            openNew,
            currentPageTitle,
            items,
            item,
            selectedItems,
            itemDialog,
            getItems,
            filters,
            hideDialog,
            saveItem,
            editItem,
            loading,
            getStatusLabel,
            icons,
            
        }
    }
}

</script>


<template>

    <Head>
        <title>{{ currentPageTitle }}</title>
        <meta name="description" content="Página para visualizar os usuários do Portal.">
    </Head>

    <Toast />

    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <Dialog @hide="hideDialog" :dismissableMask="true" v-model:visible="itemDialog" :style="{ width: '650px' }"
        header="Detalhes do Serviço" :modal="true">
        <Form ref="formRef" v-slot="$form" :resolver="resolver" :initialValues="item" @submit="saveItem"
            class="flex flex-col justify-center gap-4">

            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-6">
                    <label for="nome" class="block font-bold mb-3">Nome</label>
                    <InputText placeholder="Ex.: B.I" name="nome" id="nome" v-model.trim="item.nome" autofocus fluid />
                    <Message v-if="$form.nome?.invalid" severity="error" size="small" variant="simple">
                        {{
                            $form.nome.error?.message }}</Message>
                </div>
                <div class="col-span-6">
                   
                </div>
            </div>

  
         

            <div>
                <label class="">
                    <span class="block font-bold mb-4">Ativo?</span>
                    <ToggleSwitch name="status" v-model="item.status" />
                </label>
            </div>



            <div class="flex gap-4 ms-auto">
                <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Salvar" type="submit" icon="pi pi-check" />
            </div>
        </Form>
    </Dialog>




    <div class="overflow-hidden border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] min-h-[400px]">
        <Toolbar class="mb-6">
            <template #start>
                <Button label="Novo" icon="pi pi-plus" class="mr-2" @click="openNew" />
                <!-- <Button label="Delete" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected"
                    :disabled="!selectedItems || !selecteditems.length" /> -->
            </template>

            <template #end>
                <!-- <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" customUpload
                    chooseLabel="Import" class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" />
                <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" /> -->
                <Button label="Recarregar" icon="pi pi-refresh" severity="secondary"
                    @click="() => { getItems() }" />
            </template>
        </Toolbar>
        <DataTable ref="dt" v-model:selection="selectedItems" :value="items" dataKey="id" :paginator="true"
            :rows="$page.props.defaultEntries" :filters="filters"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="$page.props.maxEntries"
            currentPageReportTemplate="Mostrando {first} até {last} de {totalRecords} itens" :loading="loading">
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h4 class="m-0">
                        Gerenciar {{ currentPageTitle }}
                    </h4>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Procurar..." />
                    </IconField>
                </div>
            </template>

            <Column selectionMode="multiple" style="width: 3rem" :exportable="false" />

            <Column field="nome" header="Nome" sortable style="min-width: 16rem" />

            <Column field="status" header="Status" sortable style="min-width: 12rem">
                <template #body="slotProps">
                    <Tag :value="slotProps.data.status ? 'Ativo' : 'Inativo'"
                        :severity="getStatusLabel(slotProps.data.status)" />
                </template>
            </Column>

            <Column field="updated_at" header="Atualizado Em" sortable dataType="date" style="min-width: 10rem" />
            <Column field="created_at" header="Criado Em" sortable dataType="date" style="min-width: 10rem" />
            <Column header="Ações" :exportable="false" style="min-width: 12rem">
                <template #body="slotProps">
                    <Button  icon="pi pi-pencil" outlined rounded class="mr-2"
                        @click="editItem(slotProps.data, slotProps.index)" />
                </template>
            </Column>
        </DataTable>

    </div>


</template>