<template>
  <div class="settings-container">
    <div class="settings-header">
      <h1>Room Prompt Settings</h1>
      <p>Customize AI prompts for different room types</p>
    </div>

    <div class="prompts-grid">
      <div v-for="roomType in roomTypes" :key="roomType" class="prompt-card">
        <h3>{{ roomType.charAt(0).toUpperCase() + roomType.slice(1) }}</h3>
        <textarea 
          v-model="prompts[roomType]" 
          :placeholder="`Enter prompt for ${roomType}...`"
          rows="6"
        ></textarea>
      </div>
    </div>

    <div class="credits-section">
      <h2>AI Usage Statistics</h2>
      <div class="credits-grid">
        <div class="credit-card">
          <h4>Total Cost</h4>
          <p class="cost">${{ credits.total_cost || '0.00' }}</p>
          <span>{{ credits.total_generations || 0 }} generations</span>
        </div>
        <div class="credit-card">
          <h4>Today's Usage</h4>
          <p class="cost">${{ credits.today_cost || '0.00' }}</p>
          <span>{{ credits.today_generations || 0 }} generations</span>
        </div>
      </div>
    </div>

    <div class="settings-actions">
      <button @click="savePrompts" :disabled="isSaving" class="save-btn">
        {{ isSaving ? 'Saving...' : 'Save Prompts' }}
      </button>
      <button @click="resetToDefaults" class="reset-btn">Reset to Defaults</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SettingsPage',
  props: {
    prompts: Object
  },
  data() {
    return {
      isSaving: false,
      credits: {},
      roomTypes: ['bedroom', 'living-room', 'kitchen', 'dining-room'],
      prompts: {
        'bedroom': this.prompts?.bedroom?.prompt || 'Transform this bedroom into a {roomStyle} style. Keep the room layout but redesign with {roomStyle} furniture, colors, and decor elements suitable for a comfortable sleeping space.',
        'living-room': this.prompts?.['living-room']?.prompt || 'Transform this living room into a {roomStyle} style. Keep the room layout but redesign with {roomStyle} furniture, colors, and decor elements suitable for relaxation and entertainment.',
        'kitchen': this.prompts?.kitchen?.prompt || 'Transform this kitchen into a {roomStyle} style. Keep the room layout but redesign with {roomStyle} furniture, colors, and decor elements suitable for cooking and dining.',
        'dining-room': this.prompts?.['dining-room']?.prompt || 'Transform this dining room into a {roomStyle} style. Keep the room layout but redesign with {roomStyle} furniture, colors, and decor elements suitable for dining and entertaining.'
      }
    }
  },
  mounted() {
    this.loadCredits();
  },
  methods: {
    async savePrompts() {
      this.isSaving = true;
      try {
        const promptsArray = this.roomTypes.map(roomType => ({
          room_type: roomType,
          prompt: this.prompts[roomType]
        }));

        const response = await fetch('/api/update-prompts', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ prompts: promptsArray })
        });

        if (response.ok) {
          alert('Prompts saved successfully!');
        } else {
          alert('Failed to save prompts');
        }
      } catch (error) {
        alert('Error saving prompts: ' + error.message);
      } finally {
        this.isSaving = false;
      }
    },
    resetToDefaults() {
      this.prompts = {
        'bedroom': 'Transform this bedroom into a {roomStyle} style. Keep the room layout but redesign with {roomStyle} furniture, colors, and decor elements suitable for a comfortable sleeping space.',
        'living-room': 'Transform this living room into a {roomStyle} style. Keep the room layout but redesign with {roomStyle} furniture, colors, and decor elements suitable for relaxation and entertainment.',
        'kitchen': 'Transform this kitchen into a {roomStyle} style. Keep the room layout but redesign with {roomStyle} furniture, colors, and decor elements suitable for cooking and dining.',
        'dining-room': 'Transform this dining room into a {roomStyle} style. Keep the room layout but redesign with {roomStyle} furniture, colors, and decor elements suitable for dining and entertaining.'
      };
    },
    async loadCredits() {
      try {
        const response = await fetch('/api/credits');
        this.credits = await response.json();
      } catch (error) {
        console.error('Failed to load credits:', error);
      }
    }
  }
}
</script>

<style scoped>
.settings-container {
  min-height: 100vh;
  background: #1a1d29;
  color: white;
  padding: 40px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.settings-header {
  text-align: center;
  margin-bottom: 40px;
}

.settings-header h1 {
  font-size: 32px;
  margin: 0 0 10px 0;
  color: #ffffff;
}

.settings-header p {
  color: #8b8d97;
  font-size: 16px;
  margin: 0;
}

.prompts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 30px;
  margin-bottom: 40px;
}

.prompt-card {
  background: #252836;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.prompt-card h3 {
  margin: 0 0 16px 0;
  color: #ffffff;
  font-size: 18px;
  font-weight: 600;
}

.prompt-card textarea {
  width: 100%;
  background: #1a1d29;
  border: 2px solid #3a3d4a;
  border-radius: 8px;
  padding: 12px;
  color: white;
  font-size: 14px;
  line-height: 1.5;
  resize: vertical;
  min-height: 120px;
}

.prompt-card textarea:focus {
  outline: none;
  border-color: #7c3aed;
}

.settings-actions {
  display: flex;
  justify-content: center;
  gap: 20px;
}

.save-btn, .reset-btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.save-btn {
  background: linear-gradient(135deg, #84cc16, #65a30d);
  color: white;
}

.save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.reset-btn {
  background: #374151;
  color: white;
}

.reset-btn:hover {
  background: #4b5563;
}

.credits-section {
  margin-bottom: 40px;
}

.credits-section h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #ffffff;
}

.credits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.credit-card {
  background: #252836;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  border: 2px solid #3a3d4a;
}

.credit-card h4 {
  margin: 0 0 10px 0;
  color: #8b8d97;
  font-size: 14px;
}

.credit-card .cost {
  font-size: 24px;
  font-weight: bold;
  color: #84cc16;
  margin: 0 0 5px 0;
}

.credit-card span {
  color: #8b8d97;
  font-size: 12px;
}
</style>