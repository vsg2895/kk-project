<template>
    <div class="product-card card card-block" :class="{ active: quantity }">
        <icon name="star" v-if="custom"></icon>
        <icon name="driving-sign" v-else-if="addon.id == 1"></icon>
        <icon name="book-tutor" v-else-if="addon.id == 2"></icon>
        <icon name="book-driving" v-else-if="addon.id == 3"></icon>
        <icon name="ticket" v-else-if="addon.id == 4 || addon.id == 5 || addon.id == 6"></icon>
        <icon name="glasses" v-else-if="addon.id == 7"></icon>
        <icon name="reflex-vest" v-else-if="addon.id == 8"></icon>

        <h3>{{ addon.name }}</h3>
        <p :data-desc="description">
          {{ description.length > 21 ? description.substr(0, 21) : description}}
          <span v-if="description.length > 21" @click="showAllText" style="cursor:pointer;">...</span>
        </p>
        <div class="card price-tag">+ {{ price }} kr</div>

        <div class="btn btn-block btn-outline-primary">
            <div v-if="quantity">
                <button class="minus remove-addon-btn-coursepage" @click="remove"><icon name="minus"/></button>
                <div class="quantity">{{ quantity }} st</div>
                <button class="plus add-addon-btn-coursepage" @click="add"><icon name="plus"/></button>
            </div>
            <div v-else @click="add" class="add-addon-btn-coursepage">Lägg till</div>
        </div>
    </div>
</template>

<script>
    import Icon from 'vue-components/Icon';

    export default {
        components: {
            Icon,
        },
        props: ['addon', 'onAdd', 'onRemove', 'custom', 'quantity'],
        data() {
            return {
                //quantity: 0,
                onAddCallback: () => { },
                onRemoveCallback: () => { },
            };
        },
        computed: {
            description() {
                if (this.addon) {
                    return this.custom ? this.addon.description : this.addon.pivot.description ? this.addon.pivot.description : '';
                }
            },
            price() {
                if (this.addon) {
                    return this.custom ? this.addon.price : this.addon.pivot.price;
                }
            }
        },
        methods: {
            remove() {
                this.onRemoveCallback(this.addon);
            },
            add() {
                this.onAddCallback(this.addon)
            },
            showAllText(event) {
              event.target.parentNode.innerHTML = event.target.parentNode.dataset.desc;
            }
        },
        created() {
            this.onAddCallback = this.onAdd(this.custom);
            this.onRemoveCallback = this.onRemove(this.custom);
        }
    }
</script>
