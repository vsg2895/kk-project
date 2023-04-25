<template>
  <div ref="loyalty-progress">
    <div class="col-lg-6 col-xl-3 mb-1">
      <semantic-dropdown
        v-if="isDropdownShown"
        :on-item-selected="setSelectedSchool"
        form-name="school_id"
        :readonly="false"
        :initial-item="selectedSchool"
        :data="schools"
        placeholder="Välj trafikskola"
        search>
        <template slot="dropdown-item" slot-scope="props">
            <div class="item" :data-value="props.item.id">
            <div class="item-text">{{ props.item.name }} ({{ props.item.postal_city }})</div>
            </div>
        </template>
      </semantic-dropdown>
    </div>
    <div class="loyalty-progress col-xs-12">
      <div class="block-title">Lojalitetsprogram - {{ currentLevel }}</div>
      <div class="d-flex justify-content-between mb-1">
        <div
          v-for="level in loyaltyLevelsSorted"
          :key="level.id"
          :style="labelContainerStyle"
          class="label-container">
          <span class="label">{{ level.label }}</span>
        </div>
      </div>
      <div class="progress-container" :title="revenue">
        <div class="revenue-label">Omsättning</div>
        <div v-if="revenue > 0" class="loyalty-progress-bar" :style="progressBarStyle" />
      </div>
      <div class="progress-ammounts">
        <span
          v-for="(ammount, index) in progressAmmountsFormatted"
          :key="ammount"
          :style="getAmmountStyle(index)"
          class="ammount">{{ ammount }}</span>
      </div>
      <div v-if="showDescriptions" @click="isDescriptionsShown = !isDescriptionsShown" class="description-toggle">
        {{ !isDescriptionsShown ? '+ För mer ' : '- Dölj ' }}info om medlemsnivåerna
      </div>
      <div v-if="isDescriptionsShown" class="progress-description-container mt-2">
        <div
          v-for="level in loyaltyLevelsSorted"
          :key="level.id"
          :style="labelContainerStyle"
          class="progress-description">
          <span class="title">{{ `${level.label} medlem` }}</span>
          <div v-html="level.description" class="description" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Api from 'vue-helpers/Api';
import { max, sortBy } from 'lodash';
import SemanticDropdown from 'vue-components/SemanticDropdown';

export default {
  name: 'LoyaltyProgress',
  components: { SemanticDropdown },
  props: {
    loyaltyLevels: { type: Object, default: () => ({}) },
    showDescriptions: { type: Boolean, default: true },
    schools: { type: Array, default: () => ([])},
    showMore: { type: Boolean, default: false },
  },
  data: () => ({
    selectedSchool: null,
    revenue: 0,
    isDescriptionsShown: false,
    showMore: false
  }),
  computed: {
    loyaltyLevelsSorted: ({ loyaltyLevels }) => sortBy(Object.entries(loyaltyLevels).map(([key, value]) => ({ id: key, ...value })), 'start_revenue'),
    progressAmmounts: ({ loyaltyLevelsSorted }) => [0, ...loyaltyLevelsSorted.map(level => level.end_revenue)],
    progressAmmountsFormatted: ({ loyaltyLevelsSorted }) => [0, ...loyaltyLevelsSorted.map(level => level.end_revenue)].map(it => {
      return new Intl.NumberFormat('sv-SV', { style: 'currency', currency: 'SEK' }).format(it);
    }),
    currentLevel: ({ loyaltyLevelsSorted, revenue }) => loyaltyLevelsSorted.find(level => revenue >= level.start_revenue && revenue <= level.end_revenue).label,
    // progressBarStyle: ({ progressAmmounts, revenue }) => ({ width: `${(revenue / max(progressAmmounts)) * 100}%` }),
     progressBarStyle: ({ progressAmmounts, revenue }) => ({ width: `${revenue <= 750000 ? (revenue / 1000000) * 100 : 75 + ((revenue - 750000) / 1250000) * 25}%` }),
    labelContainerStyle: ({ loyaltyLevelsSorted }) => ({ flexBasis: `calc(${100 / loyaltyLevelsSorted.length}% - 0.5rem)` }),
    isDropdownShown: ({ schools }) => schools.length > 1,
  },
  created() {
    this.setSelectedSchool(this.schools[0]);
    this.isDescriptionsShown = this.showMore;
  },
  methods: {
    async setSelectedSchool(school) {
      this.selectedSchool = school;
      
      const { total_amount } = await Api.getSchoolLoyaltyLevel(school.id);
      this.revenue = total_amount;
    },
    async getAmmountStyle(index) {
      const multiplier = 100 / this.loyaltyLevelsSorted.length;
      const elements = document.getElementsByClassName('ammount');
      let element;
      let elementWidth;
      
      await this.$nextTick(() => {
        element = elements[index];
        elementWidth = elements[index].clientWidth;
      });

      if (index === 0) {
        element.style.left = 0;
        return;
      }

      if (index === this.progressAmmountsFormatted.length - 1) {
        element.style.right = '-8px';
        return;
      }
      
      element.style.left = `calc(${multiplier * index}% - ${elementWidth / 2}px)`;
    },
  },
}
</script>

<style lang="scss" scoped>
.loyalty-progress {
  padding-left: 6rem;
  padding-right: 5rem;
}

.block-title {
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
  font-size: 1.125rem;
  font-weight: 700;
}

.label-container {
  display: flex;
  align-items: center;
  flex-shrink: 0;

  &::before, &::after {
    font-size: 2rem;
    color: #205c77;
  }

  &::before {
    content: "[ ";

  }
  &::after {
    content: " ]";
  }

    .label {
      flex: 1;
      text-align: center;
      font-size: 1.125rem;
      font-weight: 700;
      text-transform: uppercase;
    }
}

.progress-container {
  position: relative;
  height: 40px;
  border: 1px solid #205c77;
  border-radius: 10px;
  background: #ff8ec5;

  .revenue-label {
    position: absolute;
    left: -90px;
    top: 50%;
    transform: translateY(-50%);
    font-weight: 700;
  }

  .loyalty-progress-bar {
    height: 100%;
    background: #0fa746;
    border-right: inherit;
    border-radius: inherit;
    transition: all 0.2s linear;
  }
}

.progress-ammounts {
  position: relative;
  display: flex;
  justify-content: space-between;
  height: 24px;
  margin-top: 1rem;
  font-size: 14px;

  .ammount {
    position: absolute;
    font-weight: 700;
  }
}

.description-toggle {
  margin-top: 2rem;
  font-weight: 700;
  cursor: pointer;
}

.progress-description-container {
  display: flex;
  justify-content: space-between;

  .progress-description {
    flex-shrink: 0;
    padding-top: 1rem;
    font-size: 18px;

    .title {
      font-weight: 700;
    }

    .description {
      padding-top: 0.5rem;
      font-weight: 500;
    }
  }
}

@media (max-width: 767px) { // sm and down
  .loyalty-progress {
    padding-left: 0;
    padding-right: 0;
  }

  .progress-container  .revenue-label {
    left: 1rem;
  }

  .label {
    font-size: 0.75rem;
  }

  .ammount {
    font-size: 0.5rem;
  }

  .progress-description-container {
    flex-direction: column;
  }
}
</style>
