<template>
  <div>
    <div class="text-90 font-normal text-l mt-3 mb-8">
      Гибкий кэшбэк позволяет давать повышенное вознаграждение рефереру за более высокий чек реферала.<br><br>
      Добавьте диапазон и укажите минимальный чек, с которого начинает действовать кэшбэк указанного размера. Кэшбэк по чекам, не попавшим в диапазоны, будет рассчитываться по ставке по-умолчанию.
    </div>
    <div v-for="(item, key) in value" v-bind:key="`${item.key}`">
      <h4 class="text-90 font-bold text-l mb-3 mt-3">Диапазон {{ key + 1}}</h4>
      <default-field :field="{ name: 'Тип награды' }" :errors="errors">
        <template slot="field">
          <select
            :id="`value_type_${key}`"
            :name="`value_type_${key}`"
            class="w-full form-control form-select"
            :class="errorClasses"
            v-model="item.valueType"
          >
            <option value="fixed">Фиксированная</option>
            <option value="percent">Процент</option>
          </select>
        </template>
      </default-field>

      <default-field :field="{ name: 'Размер кэшбека' }" :errors="errors">
        <template slot="field">
          <input
            :id="`value_${key}`"
            :name="`value_${key}`"
            type="text"
            class="w-full form-control form-input form-input-bordered"
            :class="errorClasses"
            placeholder="Размер кэшбека"
            v-model="item.value"
          />
        </template>
      </default-field>

      <default-field :field="{ name: 'Минимальный чек' }" :errors="errors">
        <template slot="field">
          <input
            :id="`min_amount_${key}`"
            :name="`min_amount_${key}`"
            type="text"
            class="w-full form-control form-input form-input-bordered"
            :class="errorClasses"
            placeholder="Минимальный чек"
            v-model="item.condition.minAmount"
          />
        </template>
      </default-field>

      <div class="mt-3 mb-3 text-right">
        <button type="button" class="btn btn-default btn-primary mr-3" @click.prevent="removeRange(key)">
          Удалить диапазон
        </button>
      </div>
    </div>

    <div class="mt-3 mb-3">
      <button type="button" class="btn btn-default btn-primary mr-3" @click.prevent="addNewRange">
        Добавить диапазон
      </button>
    </div>
  </div>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || []
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, JSON.stringify(this.value))
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value
        },

        addNewRange() {
          if (!Array.isArray(this.value)) {
            this.value = []
          }

          this.value.push({
            value: 0,
            condition: {
              minAmount: 0
            },
            valueType: "percent"
          })
        },

        removeRange(index) {
          this.value.splice(index, 1)
        }
    },
}
</script>
