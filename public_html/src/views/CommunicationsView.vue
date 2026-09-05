<template>
  <main class="comm-main-viewport">
    <div class="comm-container">
      
      <!-- Top Navigation & Tab Switcher Header -->
      <div class="comm-header">
        <div class="comm-title-area">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-600 flex items-center justify-center shadow-lg shadow-purple-600/30 border border-purple-400/30">
              <span class="material-symbols-outlined text-white text-2xl">forum</span>
            </div>
            <div>
              <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight leading-tight">
                Communications
              </h1>
              <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                {{ activeTab === 'messages' ? 'View and manage your conversations, announcements, and direct messages.' : 'Review, reply, and manage public contact inquiries.' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Top Switcher Buttons (Messages vs Inquiries) -->
        <div v-if="hasInquiriesFeature" class="comm-tab-switcher">
          <!-- Messages Tab Button -->
          <button
            type="button"
            @click="switchTab('messages')"
            class="tab-btn"
            :class="{ active: activeTab === 'messages' }"
            id="tab-btn-messages"
            aria-label="Switch to Messages"
          >
            <span class="material-symbols-outlined text-lg">mail</span>
            <span>Messages</span>
            <span 
              v-if="unreadMessagesTotal > 0" 
              class="tab-badge"
            >
              {{ unreadMessagesTotal > 99 ? '99+' : unreadMessagesTotal }}
            </span>
          </button>

          <!-- Inquiries Tab Button -->
          <button
            type="button"
            @click="switchTab('inquiries')"
            class="tab-btn"
            :class="{ active: activeTab === 'inquiries' }"
            id="tab-btn-inquiries"
            aria-label="Switch to Inquiries"
          >
            <span class="material-symbols-outlined text-lg">contact_mail</span>
            <span>Inquiries</span>
            <span 
              v-if="unreadInquiriesTotal > 0" 
              class="tab-badge"
            >
              {{ unreadInquiriesTotal > 99 ? '99+' : unreadInquiriesTotal }}
            </span>
          </button>
        </div>
      </div>

      <!-- Smooth Vue Transition Area for Tab Content -->
      <transition name="comm-tab-fade" mode="out-in">
        
        <!-- TAB 1: MESSAGES VIEW -->
        <div v-if="activeTab === 'messages'" key="tab-messages" class="comm-tab-panel">
          
          <!-- Optional Notice for TWG -->
          <div v-if="isTWG" class="info-note-twg">
            <div class="info-note-icon">
              <span class="material-symbols-outlined">info</span>
            </div>
            <div>
              <h4>Note</h4>
              <p>For document inquiries, it's recommended to send a message to the <strong>Director</strong>. For general concerns, it's recommended to send a message to the <strong>Staff</strong>.</p>
            </div>
          </div>

          <!-- Main Messenger Layout -->
          <div class="messenger-container">
            
            <!-- Left Sidebar -->
            <div class="messenger-sidebar" :class="{ 'hidden-on-mobile': rightPaneMode !== 'none' }">
              <div class="sidebar-top">
                <div class="sidebar-header">
                  <h2>Chats</h2>
                  <div class="sidebar-actions">
                    <button @click="toggleSelectionMode" class="icon-btn" :class="{'active-selection': isSelectionMode}" title="Select Multiple">
                      <span class="material-symbols-outlined">checklist</span>
                    </button>
                    <button @click="fetchMessages" class="icon-btn" title="Refresh">
                      <span class="material-symbols-outlined">refresh</span>
                    </button>
                    <button v-if="hasAnnouncementFeature" @click="rightPaneMode = 'announce'" class="icon-btn" title="Make Announcement">
                      <span class="material-symbols-outlined">campaign</span>
                    </button>
                    <button @click="openCompose" class="icon-btn primary-icon-btn" title="New Message">
                      <span class="material-symbols-outlined">edit_square</span>
                    </button>
                  </div>
                </div>
                
                <div class="sidebar-tabs">
                  <button :class="{ active: activeMsgTab === 'chats' }" @click="activeMsgTab = 'chats'">All Chats</button>
                  <button :class="{ active: activeMsgTab === 'trash' }" @click="activeMsgTab = 'trash'">Trash</button>
                </div>
              </div>
              
              <div class="conversation-list custom-scrollbar">
                <div v-if="loadingMessages" class="loading-state">
                  <div class="spinner"></div>
                </div>
                <div v-else-if="filteredConversations.length === 0" class="empty-state">
                  <span class="material-symbols-outlined">{{ activeMsgTab === 'trash' ? 'delete' : 'forum' }}</span>
                  <p>No conversations found</p>
                </div>
                <div 
                  v-else 
                  v-for="conv in filteredConversations" 
                  :key="conv.thread_id" 
                  class="conversation-item" 
                  :class="{ active: activeConversationId === conv.thread_id && rightPaneMode === 'chat', selected: selectedThreads.includes(conv.thread_id) }"
                  @click="selectConversation(conv)"
                >
                  <div v-if="isSelectionMode" class="conv-checkbox">
                    <input type="checkbox" :checked="selectedThreads.includes(conv.thread_id)" @click="toggleThreadSelection(conv.thread_id, $event)">
                  </div>
                  <div class="conv-avatar">
                    <span class="material-symbols-outlined">person</span>
                    <div v-if="conv.unread_count > 0 && activeMsgTab !== 'trash'" class="unread-badge"></div>
                  </div>
                  <div class="conv-details">
                    <div class="conv-header">
                      <span class="conv-name" :class="{ unread: conv.unread_count > 0 && activeMsgTab !== 'trash' }">{{ conv.other_party }}</span>
                      <span class="conv-time">{{ conv.latest_time_short }}</span>
                    </div>
                    <div class="conv-role">{{ conv.role }} &bull; {{ getOfficeName(conv.office_id) }}</div>
                    <div class="conv-preview" :class="{ unread: conv.unread_count > 0 && activeMsgTab !== 'trash' }">
                      <span v-if="conv.is_announcement" class="material-symbols-outlined align-middle text-amber-400 text-sm mr-1">campaign</span>
                      {{ conv.preview }}
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Bulk Action Toolbar -->
              <div v-if="isSelectionMode" class="bulk-action-toolbar">
                <span class="selected-count">{{ selectedThreads.length }} selected</span>
                <div class="bulk-actions">
                  <button v-if="activeMsgTab === 'chats'" @click="bulkTrash" :disabled="selectedThreads.length === 0" class="bulk-btn trash-btn">Trash</button>
                  <button v-if="activeMsgTab === 'trash'" @click="bulkRestore" :disabled="selectedThreads.length === 0" class="bulk-btn restore-btn">Restore</button>
                  <button v-if="activeMsgTab === 'trash'" @click="bulkPermanentlyDelete" :disabled="selectedThreads.length === 0" class="bulk-btn delete-btn">Delete</button>
                </div>
              </div>
            </div>

            <!-- Right Chat / Compose / Announce Pane -->
            <div class="messenger-chat-pane" :class="{ 'hidden-on-mobile': rightPaneMode === 'none' }">
              
              <!-- State: NO CHAT SELECTED -->
              <div v-if="rightPaneMode === 'none'" class="no-chat-selected">
                <div class="no-chat-icon"><span class="material-symbols-outlined">forum</span></div>
                <h3>Your Messages</h3>
                <p>Select a conversation from the sidebar or start a new one.</p>
                <button @click="openCompose" class="mt-4 px-4 py-2 rounded-xl bg-purple-600/30 border border-purple-500/40 text-purple-200 text-xs font-bold hover:bg-purple-600/50 transition-colors flex items-center gap-2">
                  <span class="material-symbols-outlined text-base">edit_square</span>
                  Start New Message
                </button>
              </div>

              <!-- State: COMPOSE MESSAGE -->
              <div v-else-if="rightPaneMode === 'compose'" class="compose-pane">
                <div class="chat-header">
                  <div class="chat-header-info flex items-center gap-2">
                    <button class="mobile-back-btn" @click="rightPaneMode = 'none'"><span class="material-symbols-outlined">arrow_back</span></button>
                    <h3>New Message</h3>
                  </div>
                  <div class="chat-header-actions">
                    <button @click="rightPaneMode = 'none'" class="icon-btn" title="Cancel"><span class="material-symbols-outlined">close</span></button>
                  </div>
                </div>
                
                <div class="compose-body custom-scrollbar">
                  <div class="form-group">
                    <label class="form-label">Select Recipients:</label>
                    
                    <div class="filter-group">
                      <label class="sub-label">Filters (Optional):</label>
                      <div class="role-buttons">
                        <button v-for="role in availableRoles" :key="role.value" @click="selectRole(role.value)" :class="['role-btn', { active: selectedRole === role.value }]">
                          {{ role.label }}
                        </button>
                      </div>
                      <div v-if="selectedRole && selectedRole !== 'Director' && selectedRole !== 'Staff' && !isTWG" class="office-buttons mt-2">
                        <button v-for="office in offices" :key="office.value" @click="selectOffice(office.value)" :class="['office-btn', { active: selectedOffice === office.value }]">
                          {{ office.label }}
                        </button>
                      </div>
                    </div>

                    <div class="filter-group">
                      <label class="sub-label">Search Users by Name or Email:</label>
                      <input v-model="userSearchQuery" type="text" class="form-input mb-2" placeholder="Type to search...">
                      
                      <div class="user-list custom-scrollbar">
                        <label v-for="u in filteredUsers" :key="u.id" class="user-item">
                          <input type="checkbox" :value="u.id" v-model="selectedUsers">
                          <span>{{ u.full_name }} <span class="user-email" v-if="u.email">({{ u.email }})</span></span>
                        </label>
                        <div v-if="filteredUsers.length === 0" class="no-users-msg">No users found.</div>
                      </div>
                    </div>
                    
                    <div v-if="selectedUsers.length > 0" class="filter-group mt-3">
                      <label class="sub-label">Selected Recipients:</label>
                      <div class="flex flex-wrap gap-2">
                        <span v-for="userId in selectedUsers" :key="'sel_' + userId" class="selected-user-chip">
                          {{ getUserName(userId) }}
                          <span @click="removeUser(userId)" class="material-symbols-outlined text-sm cursor-pointer">close</span>
                        </span>
                      </div>
                    </div>
                  </div>

                  <div v-if="selectedUsers.length > 0" class="form-group">
                    <label class="form-label">Attach Documents (Optional):</label>
                    <div class="filter-group">
                      <input v-model="documentSearchQuery" type="text" class="form-input mb-2" placeholder="Search documents by title, name, or email...">
                    </div>
                    <div class="checkbox-list custom-scrollbar">
                      <label v-for="doc in filteredDocuments" :key="doc.id" class="doc-item-row">
                        <input type="checkbox" :value="doc.id" v-model="selectedDocuments" class="accent-purple-600 mt-1">
                        <div class="flex flex-col">
                          <span class="text-white text-sm font-medium">{{ doc.title }} <span class="text-slate-400 text-xs font-normal">({{ doc.type }})</span></span>
                          <span class="text-slate-400 text-xs mt-0.5">Submitted by: {{ doc.submitter }} <span v-if="doc.email" class="text-slate-500">({{ doc.email }})</span></span>
                        </div>
                      </label>
                      <div v-if="filteredDocuments.length === 0" class="text-slate-400 text-sm p-2 text-center">No documents found.</div>
                    </div>
                  </div>

                  <div v-if="selectedUsers.length > 0" class="form-group">
                    <label class="form-label">Message:</label>
                    <textarea v-model="messageText" class="form-textarea" rows="4" placeholder="Enter your message"></textarea>
                  </div>
                  
                  <div v-if="messageText" class="panel-footer pb-8">
                    <button @click="sendMessage" class="btn-primary w-full">Send Message</button>
                  </div>
                </div>
              </div>

              <!-- State: MAKE ANNOUNCEMENT -->
              <div v-else-if="rightPaneMode === 'announce'" class="compose-pane">
                <div class="chat-header">
                  <div class="chat-header-info flex items-center gap-2">
                    <button class="mobile-back-btn" @click="rightPaneMode = 'none'"><span class="material-symbols-outlined">arrow_back</span></button>
                    <h3 class="text-purple-300 flex items-center gap-2">
                      <span class="material-symbols-outlined text-purple-400">campaign</span> Make Announcement
                    </h3>
                  </div>
                  <div class="chat-header-actions">
                    <button @click="rightPaneMode = 'none'" class="icon-btn" title="Cancel"><span class="material-symbols-outlined">close</span></button>
                  </div>
                </div>
                
                <div class="compose-body custom-scrollbar">
                  <div class="form-group">
                    <label class="form-label">Target Audience:</label>
                    <select v-model="announceTargetType" class="form-control">
                      <option value="">Select Audience...</option>
                      <option value="all">Whole University</option>
                      <option value="role">Specific Role</option>
                      <option value="office">Specific Office / Unit</option>
                    </select>
                  </div>

                  <div v-if="announceTargetType === 'role'" class="form-group">
                    <label class="form-label">Select Role:</label>
                    <select v-model="announceTargetValue" class="form-control">
                      <option value="">Select Role...</option>
                      <option v-for="r in availableRoles" :key="r.value" :value="r.value">{{ r.label }}</option>
                    </select>
                  </div>

                  <div v-if="announceTargetType === 'office'" class="form-group">
                    <label class="form-label">Select Office:</label>
                    <select v-model="announceTargetValue" class="form-control">
                      <option value="">Select Office...</option>
                      <option v-for="office in offices" :key="office.value" :value="office.value">{{ office.label }}</option>
                    </select>
                  </div>

                  <div v-if="announceTargetType && (announceTargetType === 'all' || announceTargetValue)" class="form-group">
                    <label class="form-label">Message:</label>
                    <textarea v-model="announceMessage" class="form-textarea" rows="6" placeholder="Enter your announcement details"></textarea>
                  </div>

                  <div v-if="announceMessage" class="panel-footer pb-8">
                    <button @click="sendAnnouncement" class="btn-primary w-full" style="background: linear-gradient(135deg, #9333ea, #c084fc);">
                      Broadcast Announcement
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- State: ACTIVE CHAT -->
              <div v-else-if="rightPaneMode === 'chat' && activeConversationId" class="active-chat-wrapper">
                <!-- Chat Header -->
                <div class="chat-header">
                  <div class="chat-header-info flex items-center gap-2">
                    <button class="mobile-back-btn" @click="rightPaneMode = 'none'"><span class="material-symbols-outlined">arrow_back</span></button>
                    <div>
                      <h3>{{ activeConversation?.other_party }}</h3>
                      <span class="chat-role">{{ activeConversation?.role }} &bull; {{ getOfficeName(activeConversation?.office_id) }}</span>
                    </div>
                  </div>
                  <div class="chat-header-actions">
                    <button v-if="activeMsgTab !== 'trash'" @click="trashConversation(activeConversation?.thread_id)" class="icon-btn danger-icon-btn" title="Move to Trash">
                      <span class="material-symbols-outlined">delete</span>
                    </button>
                    <button v-if="activeMsgTab === 'trash'" @click="restoreConversation(activeConversation?.thread_id)" class="icon-btn success-icon-btn" title="Restore Chat">
                      <span class="material-symbols-outlined">restore_from_trash</span>
                    </button>
                    <button v-if="activeMsgTab === 'trash'" @click="permanentlyDeleteConversation(activeConversation?.thread_id)" class="icon-btn danger-icon-btn" title="Permanently Delete">
                      <span class="material-symbols-outlined">delete_forever</span>
                    </button>
                  </div>
                </div>
                
                <!-- Chat History -->
                <div class="chat-history custom-scrollbar" ref="chatHistoryContainer">
                  <div v-if="loadingThread" class="loading-state"><div class="spinner"></div></div>
                  <div v-else class="chat-bubbles-container">
                    <div 
                      v-for="msg in activeThreadHistory" 
                      :key="msg.id" 
                      class="chat-message-row"
                      :class="{ 'sent': msg.sender_id == user.id, 'received': msg.sender_id != user.id }"
                    >
                      <div class="chat-bubble">
                        <div class="msg-text">{{ msg.message }}</div>
                        
                        <div v-if="msg.document_id" class="msg-attachments">
                          <div class="attachment-label"><span class="material-symbols-outlined">attachment</span> Attachments</div>
                          <div class="attachment-list">
                            <button 
                              v-for="docId in msg.document_id.split(',')" 
                              :key="docId" 
                              @click="handleViewAttachedDocument(docId)" 
                              class="attachment-btn"
                            >
                              View {{ docId.startsWith('design_') ? 'Design' : 'Report' }}
                            </button>
                          </div>
                        </div>
                        
                        <div class="msg-time">{{ msg.date }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Chat Composer (Hidden if announcement) -->
                <div class="chat-composer" v-if="activeMsgTab !== 'trash' && !activeConversation?.is_announcement">
                  <div v-if="composerDocuments.length > 0" class="composer-attachments-preview">
                    <span v-for="docId in composerDocuments" :key="docId" class="doc-chip">
                      {{ getDocTitle(docId) }}
                      <span @click="composerDocuments = composerDocuments.filter(id => id !== docId)" class="material-symbols-outlined">close</span>
                    </span>
                  </div>
                  
                  <div class="composer-input-area">
                    <button @click="showAttachmentMenu = !showAttachmentMenu" class="icon-btn attach-btn" title="Attach Document">
                      <span class="material-symbols-outlined">attach_file</span>
                    </button>
                    
                    <textarea 
                      v-model="composerText" 
                      @keydown.enter.prevent="handleEnter"
                      placeholder="Type a message..." 
                      rows="1"
                      ref="composerTextarea"
                      @input="adjustTextareaHeight"
                      class="composer-textarea"
                    ></textarea>
                    
                    <button @click="sendReply" :disabled="!composerText.trim() && composerDocuments.length === 0" class="send-btn" title="Send">
                      <span class="material-symbols-outlined">send</span>
                    </button>
                  </div>
                  
                  <!-- Attachment picker menu -->
                  <div v-if="showAttachmentMenu" class="attachment-menu custom-scrollbar">
                    <div class="attachment-menu-header">
                      <h4>Select Documents</h4>
                      <button @click="showAttachmentMenu = false"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div class="attachment-menu-list custom-scrollbar">
                      <label v-for="doc in activeChatUserDocs" :key="doc.id" class="doc-checkbox">
                        <input type="checkbox" :value="doc.id" v-model="composerDocuments">
                        <span>{{ doc.title }} ({{ doc.type }})</span>
                      </label>
                      <div v-if="activeChatUserDocs.length === 0" class="no-docs">No documents found.</div>
                    </div>
                  </div>
                </div>
                
                <div class="announcement-banner" v-else-if="activeConversation?.is_announcement">
                  <span class="material-symbols-outlined">campaign</span>
                  <p>This is a broadcast announcement. Replies are disabled.</p>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- TAB 2: INQUIRIES VIEW -->
        <div v-else-if="activeTab === 'inquiries'" key="tab-inquiries" class="comm-tab-panel">
          
          <!-- Inquiries Controls & Search Bar -->
          <div class="inquiries-top-bar mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-400">contact_mail</span>
                Public Inquiries
              </h2>
              <p class="text-xs text-slate-400">Manage inquiries received from the public website contact form.</p>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <input 
                v-model="inquirySearchQuery" 
                type="text" 
                placeholder="Search inquiries..." 
                class="form-input w-full sm:w-64 text-xs"
              />
              <button @click="fetchInquiries" class="btn-secondary flex items-center gap-1.5 text-xs py-2 px-3">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                Refresh
              </button>
            </div>
          </div>

          <!-- Stats Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="glass-card p-5 flex items-center gap-4">
              <div class="w-11 h-11 rounded-2xl bg-blue-900/40 text-blue-400 flex items-center justify-center border border-blue-500/20">
                <span class="material-symbols-outlined text-2xl">inbox</span>
              </div>
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Inquiries</p>
                <p class="text-2xl font-black text-white">{{ inquiries.length }}</p>
              </div>
            </div>
            
            <div class="glass-card p-5 flex items-center gap-4">
              <div class="w-11 h-11 rounded-2xl bg-amber-900/40 text-amber-400 flex items-center justify-center border border-amber-500/20">
                <span class="material-symbols-outlined text-2xl">mark_email_unread</span>
              </div>
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Unread Inquiries</p>
                <p class="text-2xl font-black text-white">{{ unreadInquiriesTotal }}</p>
              </div>
            </div>

            <div class="glass-card p-5 flex items-center gap-4 sm:col-span-2 lg:col-span-1">
              <div class="w-11 h-11 rounded-2xl bg-purple-900/40 text-purple-400 flex items-center justify-center border border-purple-500/20">
                <span class="material-symbols-outlined text-2xl">quickreply</span>
              </div>
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Replied</p>
                <p class="text-2xl font-black text-white">{{ repliedInquiriesCount }}</p>
              </div>
            </div>
          </div>

          <!-- Inquiries Grid Container -->
          <div class="glass-card overflow-hidden rounded-2xl border border-purple-500/20">
            <!-- Loading State -->
            <div v-if="loadingInquiries" class="p-12 flex flex-col items-center justify-center text-slate-400">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mb-4"></div>
              <p class="text-sm">Loading inquiries...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="filteredInquiries.length === 0" class="p-12 flex flex-col items-center justify-center text-slate-400">
              <span class="material-symbols-outlined text-5xl mb-3 opacity-40 text-purple-300">mail</span>
              <h3 class="text-base font-semibold text-white mb-1">No Inquiries Found</h3>
              <p class="text-xs text-slate-400">Try adjusting your search criteria or refresh the list.</p>
            </div>

            <!-- Inquiries List -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-5 p-5">
              <div 
                v-for="inquiry in filteredInquiries" 
                :key="inquiry.id"
                class="p-5 transition-all duration-300 hover:bg-white/5 flex flex-col gap-3 group rounded-xl border border-purple-500/20"
                :class="{ 'bg-purple-900/20 shadow-[0_0_15px_rgba(168,85,247,0.15)]': inquiry.status === 'new', 'bg-black/30': inquiry.status !== 'new' }"
              >
                <!-- Header: Status, Date, Name -->
                <div class="flex flex-col gap-3">
                  <div class="flex items-start gap-3">
                    <span v-if="inquiry.status === 'new'" class="w-2.5 h-2.5 rounded-full bg-purple-500 mt-1.5 shadow-[0_0_8px_rgba(168,85,247,0.8)] animate-pulse"></span>
                    <span v-else-if="inquiry.status === 'replied_staff'" class="w-2.5 h-2.5 rounded-full bg-green-500 mt-1.5" title="Replied by Staff"></span>
                    <span v-else-if="inquiry.status === 'replied_director'" class="w-2.5 h-2.5 rounded-full bg-purple-400 mt-1.5" title="Replied by Director"></span>
                    <div class="min-w-0 flex-1">
                      <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="text-base font-bold text-white truncate" :class="{ 'text-purple-200': inquiry.status === 'new' }">
                          {{ inquiry.name }}
                        </h4>
                        <span class="text-[10px] font-bold text-purple-300 bg-purple-900/50 px-2 py-0.5 rounded border border-purple-500/30">
                          {{ formatTicketNumber(inquiry.id) }}
                        </span>
                      </div>
                      <a :href="'mailto:' + inquiry.email" class="text-xs text-purple-300 hover:text-purple-200 hover:underline flex items-center gap-1 mt-0.5">
                        <span class="material-symbols-outlined text-[13px]">mail</span>
                        <span class="truncate">{{ inquiry.email }}</span>
                      </a>
                    </div>
                  </div>
                  
                  <div class="flex items-center flex-wrap gap-2">
                    <span class="text-xs text-slate-300 bg-black/30 px-2.5 py-1 rounded-full whitespace-nowrap border border-white/5">
                      {{ formatDate(inquiry.created_at) }}
                    </span>
                    
                    <div v-if="inquiry.status === 'replied_staff'" class="text-[11px] font-bold px-2 py-0.5 bg-green-900/30 text-green-400 rounded-full border border-green-800">
                      Replied by Staff
                    </div>
                    <div v-if="inquiry.status === 'replied_director'" class="text-[11px] font-bold px-2 py-0.5 bg-purple-900/30 text-purple-300 rounded-full border border-purple-800">
                      Replied by Director
                    </div>

                    <div class="flex items-center gap-1.5 ml-auto">
                      <button 
                        v-if="!inquiry.status.startsWith('replied')"
                        @click="openReplyModal(inquiry)"
                        class="text-xs px-2.5 py-1 btn-primary flex items-center gap-1"
                        title="Reply"
                      >
                        <span class="material-symbols-outlined text-[15px]">reply</span> Reply
                      </button>
                      
                      <button 
                        v-if="inquiry.status === 'new'"
                        @click="markAsRead(inquiry)"
                        class="text-xs px-2.5 py-1 bg-slate-700/80 rounded-lg text-white hover:bg-slate-600 transition-colors flex items-center gap-1 border border-white/10"
                        title="Mark as Read"
                      >
                        <span class="material-symbols-outlined text-[15px]">done</span> Read
                      </button>

                      <button 
                        @click="deleteInquiry(inquiry)"
                        class="text-xs px-2 py-1 bg-red-600/80 rounded-lg text-white hover:bg-red-500 transition-colors flex items-center gap-1"
                        title="Delete Inquiry"
                      >
                        <span class="material-symbols-outlined text-[15px]">delete</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Content: Subject & Message -->
                <div class="mt-2 flex-grow flex flex-col">
                  <h5 class="text-xs font-semibold text-purple-200 mb-1.5">Subject: {{ inquiry.subject }}</h5>
                  <div class="bg-black/40 rounded-xl p-3.5 border border-white/5 text-slate-300 text-xs whitespace-pre-wrap leading-relaxed shadow-inner flex-grow">
                    {{ inquiry.message }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Reply Modal -->
          <div v-if="showReplyModal" class="modal-overlay">
            <div class="glass-card w-full max-w-lg flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-purple-500/30" style="max-height: 90vh;">
              <div class="p-4 sm:p-5 border-b border-purple-500/20 flex justify-between items-center bg-black/40">
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                  <span class="material-symbols-outlined text-purple-400">reply</span>
                  Reply to {{ activeInquiry?.name }}
                </h3>
                <button @click="closeReplyModal" class="text-slate-400 hover:text-white transition-colors">
                  <span class="material-symbols-outlined">close</span>
                </button>
              </div>
              
              <div class="p-4 sm:p-6 flex-grow overflow-y-auto custom-scrollbar bg-[#121226]/95">
                <div class="mb-4 p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl text-xs text-amber-200 flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-amber-400 text-lg flex-shrink-0 mt-0.5">warning</span>
                  <p class="leading-relaxed">
                    <strong>Note:</strong> Sending this reply will deliver an official email response directly to <em>{{ activeInquiry?.email }}</em>.
                  </p>
                </div>
                
                <div class="form-group">
                  <label class="form-label text-xs">Response Message:</label>
                  <textarea 
                    v-model="replyMessage" 
                    rows="6" 
                    class="form-input text-sm" 
                    placeholder="Type your official reply here..."
                  ></textarea>
                </div>
              </div>
              
              <div class="p-4 border-t border-purple-500/20 bg-black/40 flex justify-end gap-2.5">
                <button @click="closeReplyModal" class="btn-secondary text-xs px-4 py-2">
                  Cancel
                </button>
                <button @click="submitReply" :disabled="replying || !replyMessage.trim()" class="btn-primary text-xs px-4 py-2 flex items-center gap-2">
                  <span v-if="replying" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                  {{ replying ? 'Sending...' : 'Send Reply' }}
                </button>
              </div>
            </div>
          </div>

        </div>

      </transition>

    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import api from '../api';

const route = useRoute();
const router = useRouter();

// User & Role context
const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));
const userRole = computed(() => (user.value.role || user.value.user_role || '').toLowerCase());

const isTWG = computed(() => {
  const r = userRole.value;
  return r.includes('twg') || r.includes('college');
});

const hasAnnouncementFeature = computed(() => {
  const r = userRole.value;
  return r.includes('admin') || r.includes('director') || r.includes('staff');
});

const hasInquiriesFeature = computed(() => {
  const r = userRole.value;
  return r.includes('admin') || r.includes('director') || r.includes('staff');
});

const availableRoles = computed(() => {
  const r = userRole.value;
  if (r.includes('admin') || r.includes('director')) {
    return [
      { value: 'Staff', label: 'Staff' },
      { value: 'TWG', label: 'TWG' },
      { value: 'Non-TWG', label: 'Non-TWG' }
    ];
  }
  if (r.includes('staff')) {
    return [
      { value: 'Director', label: 'Director' },
      { value: 'TWG', label: 'TWG' },
      { value: 'Non-TWG', label: 'Non-TWG' }
    ];
  }
  return [
    { value: 'Director', label: 'Director' },
    { value: 'Staff', label: 'Staff' }
  ];
});

// View attachment router handler
const handleViewAttachedDocument = (docId) => {
  if (!docId) return;
  const isPending = docId.includes('pending') || (activeChatUserDocs.value.find(d => d.id === docId)?.title?.includes('(Pending)'));
  const r = userRole.value;
  const isDesign = docId.startsWith('design_');
  const cleanId = docId.replace('design_', '').replace('report_', '');
  
  if (r.includes('admin') || r.includes('director')) {
    if (isDesign) {
      router.push({ name: isPending ? 'admin-ad-review' : 'admin-ad-view', params: { id: cleanId } });
    } else {
      router.push({ name: isPending ? 'admin-ar-review' : 'admin-ar-view', params: { id: cleanId } });
    }
  } else if (r.includes('staff')) {
    router.push({ name: isDesign ? 'staff-ad-view' : 'staff-ar-view', params: { id: cleanId } });
  } else {
    router.push({ name: isDesign ? 'college-ad-view' : 'college-ar-view', params: { id: cleanId } });
  }
};

// ========================
// TABS SWITCHER STATE
// ========================
const activeTab = ref('messages');

const switchTab = (tabName) => {
  activeTab.value = tabName;
  router.replace({ query: { ...route.query, tab: tabName } }).catch(() => {});
};

// Sync active tab with route query
watch(() => route.query.tab, (newTab) => {
  if (newTab === 'inquiries' && hasInquiriesFeature.value) {
    activeTab.value = 'inquiries';
  } else if (newTab === 'messages') {
    activeTab.value = 'messages';
  }
}, { immediate: true });

// ========================
// MESSAGES STATE & LOGIC
// ========================
const activeMsgTab = ref('chats');
const isSelectionMode = ref(false);
const selectedThreads = ref([]);
const loadingMessages = ref(true);
const conversations = ref([]);
const trashedConversations = ref([]);
const activeConversationId = ref(null);
const activeConversation = ref(null);
const activeThreadHistory = ref([]);
const loadingThread = ref(false);
const chatHistoryContainer = ref(null);

// Right Pane Mode: 'none', 'chat', 'compose', 'announce'
const rightPaneMode = ref('none');

// Compose State
const selectedRole = ref('');
const selectedOffice = ref('');
const userSearchQuery = ref('');
const selectedUsers = ref([]);
const selectedDocuments = ref([]);
const allUserDocuments = ref([]);
const documentSearchQuery = ref('');
const messageTitle = ref('');
const messageText = ref('');
const allUsers = ref([]);
const offices = ref([]);

// Announce State
const announceTargetType = ref('');
const announceTargetValue = ref('');
const announceTitle = ref('');
const announceMessage = ref('');

// Inline Chat Composer State
const composerText = ref('');
const composerDocuments = ref([]);
const showAttachmentMenu = ref(false);
const activeChatUserDocs = ref([]);
const composerTextarea = ref(null);

const unreadMessagesTotal = computed(() => {
  return conversations.value.reduce((sum, c) => sum + (c.unread_count || 0), 0);
});

const getUserName = (id) => {
  const u = allUsers.value.find(x => x.id === id);
  return u ? u.full_name : 'Unknown';
};

const getOfficeName = (id) => {
  if (!id) return 'Unknown Office';
  const office = offices.value.find(o => String(o.value) === String(id));
  return office ? office.label : 'Unknown Office';
};

const removeUser = (id) => {
  selectedUsers.value = selectedUsers.value.filter(u => u !== id);
};

const openCompose = () => {
  rightPaneMode.value = 'compose';
  selectedRole.value = '';
  selectedOffice.value = '';
  userSearchQuery.value = '';
  selectedUsers.value = [];
  selectedDocuments.value = [];
  messageText.value = '';
};

const adjustTextareaHeight = () => {
  if (composerTextarea.value) {
    composerTextarea.value.style.height = 'auto';
    composerTextarea.value.style.height = Math.min(composerTextarea.value.scrollHeight, 120) + 'px';
  }
};

const handleEnter = (e) => {
  if (!e.shiftKey) {
    sendReply();
  } else {
    composerText.value += '\n';
    adjustTextareaHeight();
  }
};

const filteredUsers = computed(() => {
  let users = allUsers.value;
  if (selectedRole.value) {
    users = users.filter(u => u.user_role === selectedRole.value);
  }
  if (selectedOffice.value) {
    users = users.filter(u => String(u.office_id) === String(selectedOffice.value));
  }
  if (userSearchQuery.value.trim()) {
    const q = userSearchQuery.value.toLowerCase();
    users = users.filter(u => 
      (u.full_name && u.full_name.toLowerCase().includes(q)) || 
      (u.email && u.email.toLowerCase().includes(q))
    );
  }
  return users;
});

const selectRole = (roleValue) => {
  selectedRole.value = roleValue === selectedRole.value ? '' : roleValue;
  selectedOffice.value = '';
};

const selectOffice = (officeValue) => {
  selectedOffice.value = officeValue === selectedOffice.value ? '' : officeValue;
};

const fetchMyDocuments = async () => {
  if (!user.value.id) return;
  try {
    const r = user.value.user_role || user.value.role || 'TWG';
    let designsUrl = `activity-designs/${user.value.id}`;
    let reportsUrl = `activity-reports/${user.value.id}`;
    if (r === 'Director' || r === 'Admin' || userRole.value.includes('admin')) {
      designsUrl = 'activity-designs';
      reportsUrl = 'activity-reports';
    }
    const [designsRes, reportsRes, archivesRes] = await Promise.all([
      api.get(designsUrl).catch(() => ({ data: { data: [] } })),
      api.get(reportsUrl).catch(() => ({ data: { data: [] } })),
      api.get(`archives?user_id=${user.value.id}&role=${r}`).catch(() => ({ data: { data: [] } }))
    ]);
    
    const activeDesigns = (designsRes.data?.data || []).map(d => ({
      id: `design_${d.act_design_id}`, title: `${d.title} (${d.status})`, type: 'Activity Design', submitter: d.submitter_name || 'Unknown', user_id: d.user_id
    }));
    const activeReports = (reportsRes.data?.data || []).map(rep => ({
      id: `report_${rep.id}`, title: `${rep.title} (${rep.status})`, type: 'Accomplishment Report', submitter: rep.submitter_name || 'Unknown', user_id: rep.user_id
    }));
    const archivesList = archivesRes.data?.data || [];
    const archivedDesigns = archivesList.filter(a => a.type === 'design').map(d => ({
      id: `design_${d.original_id}`, title: `${d.title} (Archived)`, type: 'Activity Design', submitter: d.submitter_name || 'Unknown', user_id: d.user_id
    }));
    const archivedReports = archivesList.filter(a => a.type === 'report').map(rep => ({
      id: `report_${rep.original_id}`, title: `${rep.title} (Archived)`, type: 'Accomplishment Report', submitter: rep.submitter_name || 'Unknown', user_id: rep.user_id
    }));
    allUserDocuments.value = [...activeDesigns, ...activeReports, ...archivedDesigns, ...archivedReports];
  } catch (e) {
    console.error('Error fetching my docs:', e);
  }
};

const filteredDocuments = computed(() => {
  let docs = allUserDocuments.value.map(doc => {
    let email = '';
    if (doc.user_id) {
      const u = allUsers.value.find(x => String(x.id) === String(doc.user_id));
      if (u) email = u.email;
    }
    if (!email && doc.submitter && doc.submitter !== 'Unknown') {
      const u = allUsers.value.find(x => x.full_name === doc.submitter);
      if (u) email = u.email;
    }
    return { ...doc, email };
  });

  if (documentSearchQuery.value.trim()) {
    const q = documentSearchQuery.value.toLowerCase();
    docs = docs.filter(d => 
      (d.title && d.title.toLowerCase().includes(q)) || 
      (d.submitter && d.submitter.toLowerCase().includes(q)) ||
      (d.email && d.email.toLowerCase().includes(q))
    );
  }
  return docs;
});

const fetchUsers = async () => {
  try {
    const usersResponse = await api.get('users');
    allUsers.value = usersResponse.data;
    const officesResponse = await api.get('office_units');
    offices.value = officesResponse.data.map(office => ({
      value: office.unit_id || office.office_id,
      label: office.unit_name || office.office_name
    }));
  } catch (err) {
    console.error('Error fetching users/offices:', err);
  }
};

const processMessagesToConversations = (inbox, sent) => {
  const threads = {};
  
  const processMsg = (msg, type) => {
    const threadId = msg.parent_id || msg.id;
    const isUnread = type === 'inbox' && msg.is_read == 0;
    
    let displayParty = msg.sender;
    if (msg.is_announcement == 1) {
      if (type === 'sent' || msg.direction === 'sent') {
        displayParty = 'Broadcast Announcements';
      } else {
        const role = msg.role || 'System';
        displayParty = `${role} Announcements`;
      }
    }

    if (!threads[threadId]) {
      threads[threadId] = {
        thread_id: threadId,
        latest_msg: msg,
        other_party: displayParty,
        other_party_id: msg.sender_id,
        role: (msg.is_announcement == 1 && (type === 'sent' || msg.direction === 'sent')) ? (user.value.user_role || user.value.role) : msg.role,
        office_id: (msg.is_announcement == 1 && (type === 'sent' || msg.direction === 'sent')) ? user.value.office_id : msg.office_id,
        unread_count: isUnread ? 1 : 0,
        is_announcement: msg.is_announcement == 1
      };
    } else {
      if (new Date(msg.created_at || msg.date) > new Date(threads[threadId].latest_msg.created_at || threads[threadId].latest_msg.date)) {
        threads[threadId].latest_msg = msg;
        threads[threadId].other_party = displayParty;
        threads[threadId].other_party_id = msg.sender_id;
        threads[threadId].role = (msg.is_announcement == 1 && type === 'sent') ? (user.value.user_role || user.value.role) : msg.role;
        threads[threadId].office_id = (msg.is_announcement == 1 && type === 'sent') ? user.value.office_id : msg.office_id;
        threads[threadId].is_announcement = msg.is_announcement == 1;
      }
      if (isUnread) threads[threadId].unread_count++;
    }
  };

  (inbox || []).forEach(m => processMsg(m, 'inbox'));
  (sent || []).forEach(m => processMsg(m, 'sent'));

  return Object.values(threads).map(t => {
    const d = new Date(t.latest_msg.date);
    const now = new Date();
    let timeShort = t.latest_msg.date;
    if (d.toDateString() === now.toDateString()) {
      timeShort = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } else {
      timeShort = d.toLocaleDateString([], { month: 'short', day: 'numeric' });
    }
    
    return {
      ...t,
      latest_time_short: timeShort,
      latest_time: d,
      preview: t.latest_msg.preview || 'Attachment'
    };
  }).sort((a, b) => b.latest_time - a.latest_time);
};

const fetchMessages = async () => {
  if (user.value.id) {
    loadingMessages.value = true;
    try {
      const [inboxRes, sentRes, trashRes] = await Promise.all([
        api.get(`messages/inbox/${user.value.id}`).catch(() => ({ data: { success: false } })),
        api.get(`messages/sent/${user.value.id}`).catch(() => ({ data: { success: false } })),
        api.get(`messages/trashed/${user.value.id}`).catch(() => ({ data: { success: false } }))
      ]);
      
      if (inboxRes.data.success && sentRes.data.success) {
        conversations.value = processMessagesToConversations(inboxRes.data.data, sentRes.data.data);
      }
      if (trashRes.data.success) {
        trashedConversations.value = processMessagesToConversations(trashRes.data.data, []);
      }
    } catch (err) {
      console.error('Error fetching messages:', err);
    } finally {
      loadingMessages.value = false;
    }
  }
};

const filteredConversations = computed(() => {
  return activeMsgTab.value === 'trash' ? trashedConversations.value : conversations.value;
});

const scrollToBottom = () => {
  nextTick(() => {
    if (chatHistoryContainer.value) {
      chatHistoryContainer.value.scrollTop = chatHistoryContainer.value.scrollHeight;
    }
  });
};

const toggleSelectionMode = () => {
  isSelectionMode.value = !isSelectionMode.value;
  if (!isSelectionMode.value) {
    selectedThreads.value = [];
  }
};

const toggleThreadSelection = (threadId, event) => {
  event.stopPropagation();
  const index = selectedThreads.value.indexOf(threadId);
  if (index === -1) {
    selectedThreads.value.push(threadId);
  } else {
    selectedThreads.value.splice(index, 1);
  }
};

const bulkTrash = async () => {
  if (selectedThreads.value.length === 0) return;
  try {
    const res = await api.post('messages/bulk-trash', {
      user_id: user.value.id,
      message_ids: selectedThreads.value
    });
    if (res.data.success) {
      Swal.fire({ icon: 'success', title: 'Trashed', text: 'Selected conversations moved to trash.', timer: 1500, showConfirmButton: false });
      isSelectionMode.value = false;
      selectedThreads.value = [];
      fetchMessages();
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to trash conversations' });
  }
};

const bulkRestore = async () => {
  if (selectedThreads.value.length === 0) return;
  try {
    const res = await api.post('messages/bulk-restore', {
      user_id: user.value.id,
      message_ids: selectedThreads.value
    });
    if (res.data.success) {
      Swal.fire({ icon: 'success', title: 'Restored', text: 'Selected conversations restored.', timer: 1500, showConfirmButton: false });
      isSelectionMode.value = false;
      selectedThreads.value = [];
      fetchMessages();
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to restore conversations' });
  }
};

const bulkPermanentlyDelete = async () => {
  if (selectedThreads.value.length === 0) return;
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this! Selected conversations will be deleted permanently.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Yes, delete permanently!'
  });
  if (result.isConfirmed) {
    try {
      const res = await api.post('messages/permanently-delete', {
        user_id: user.value.id,
        message_ids: selectedThreads.value
      });
      if (res.data.success) {
        Swal.fire({ icon: 'success', title: 'Deleted', text: 'Selected conversations deleted permanently.', timer: 1500, showConfirmButton: false });
        isSelectionMode.value = false;
        selectedThreads.value = [];
        fetchMessages();
      }
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete permanently' });
    }
  }
};

const selectConversation = async (conv) => {
  if (isSelectionMode.value) {
    const index = selectedThreads.value.indexOf(conv.thread_id);
    if (index === -1) selectedThreads.value.push(conv.thread_id);
    else selectedThreads.value.splice(index, 1);
    return;
  }
  activeConversationId.value = conv.thread_id;
  activeConversation.value = conv;
  rightPaneMode.value = 'chat';
  conv.unread_count = 0;
  
  loadingThread.value = true;
  activeThreadHistory.value = [];
  composerText.value = '';
  composerDocuments.value = [];
  showAttachmentMenu.value = false;
  
  try {
    const res = await api.get(`messages/thread/${conv.thread_id}`);
    if (res.data.success) {
      activeThreadHistory.value = res.data.data;
      
      activeThreadHistory.value.forEach(msg => {
        if (String(msg.sender_id) !== String(user.value.id) && msg.is_read == 0) {
          api.post(`messages/read/${msg.id}`, {}).catch(() => {}).then(() => {
            conv.unread_count = 0; 
          });
        }
      });
      
      scrollToBottom();
      activeChatUserDocs.value = allUserDocuments.value;
    }
  } catch (err) {
    console.error('Error fetching thread:', err);
  } finally {
    loadingThread.value = false;
  }
};

const getDocTitle = (docId) => {
  const d = activeChatUserDocs.value.find(x => x.id === docId);
  return d ? d.title : docId;
};

const sendMessage = async () => {
  if (!user.value.id) return;
  try {
    let threadId = null;
    if (selectedUsers.value.length === 1) {
      const targetId = selectedUsers.value[0];
      const existingConv = conversations.value.find(c => String(c.other_party_id) === String(targetId) && !c.is_announcement);
      if (existingConv) {
        threadId = existingConv.thread_id;
      }
    }

    const payload = {
      sender_id: user.value.id,
      to: selectedUsers.value,
      parent_id: threadId,
      title: messageTitle.value,
      message: messageText.value,
      document_type: selectedDocuments.value.length > 0 ? 'Mixed' : null,
      document_id: selectedDocuments.value.length > 0 ? selectedDocuments.value.join(',') : null
    };
    
    Swal.fire({
      title: 'Sending...',
      text: 'Please wait while your message is sent and email notifications are dispatched.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    const response = await api.post('messages/send', payload);
    if (response.data.success) {
      if (threadId) {
        const existingConv = conversations.value.find(c => c.thread_id === threadId);
        if (existingConv) {
          selectConversation(existingConv);
        } else {
          rightPaneMode.value = 'none';
        }
      } else {
        rightPaneMode.value = 'none';
      }
      
      selectRole('');
      selectedOffice.value = '';
      userSearchQuery.value = '';
      selectedUsers.value = [];
      messageTitle.value = '';
      messageText.value = '';
      selectedDocuments.value = [];
      Swal.fire({ icon: 'success', title: 'Success!', text: 'Message sent successfully.', timer: 1500, showConfirmButton: false });
      fetchMessages();
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to send message.' });
  }
};

const sendAnnouncement = async () => {
  if (!user.value.id) return;
  if (!announceTargetType.value) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Please select a target audience.' });
    return;
  }
  if (announceTargetType.value !== 'all' && !announceTargetValue.value) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Please select a specific role or office.' });
    return;
  }
  if (!announceMessage.value) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Please fill in all fields.' });
    return;
  }

  try {
    const payload = {
      sender_id: user.value.id,
      target_type: announceTargetType.value,
      target_value: announceTargetValue.value,
      title: announceTitle.value,
      message: announceMessage.value
    };
    
    Swal.fire({
      title: 'Sending Announcement...',
      text: 'Please wait while your announcement is broadcasted.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    const response = await api.post('messages/announce', payload);
    if (response.data.success) {
      rightPaneMode.value = 'none';
      announceTargetType.value = '';
      announceTargetValue.value = '';
      announceTitle.value = '';
      announceMessage.value = '';
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Announcement broadcasted successfully.',
        confirmButtonColor: '#3b82f6'
      });
      fetchMessages();
    }
  } catch (err) {
    console.error('Error sending announcement:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to send announcement.' });
  }
};

const sendReply = async () => {
  if (!composerText.value.trim() && composerDocuments.value.length === 0) return;
  if (!activeConversation.value) return;

  const originalTitle = activeThreadHistory.value[0]?.title || '';
  const replyTitle = originalTitle.startsWith('Re:') ? originalTitle : (originalTitle ? `Re: ${originalTitle}` : 'Re: Message');

  try {
    const payload = {
      sender_id: user.value.id,
      to: [activeConversation.value.other_party_id],
      parent_id: activeConversation.value.thread_id,
      title: replyTitle,
      message: composerText.value.trim(),
      document_type: composerDocuments.value.length > 0 ? 'Mixed' : null,
      document_id: composerDocuments.value.length > 0 ? composerDocuments.value.join(',') : null
    };
    
    Swal.fire({
      title: 'Sending...',
      text: 'Please wait while your reply is sent and email notifications are dispatched.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    const response = await api.post('messages/send', payload);
    Swal.close();
    if (response.data.success) {
      composerText.value = '';
      composerDocuments.value = [];
      showAttachmentMenu.value = false;
      adjustTextareaHeight();
      
      selectConversation(activeConversation.value);
      fetchMessages(); 
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to send reply.' });
  }
};

const trashConversation = async (threadId) => {
  if (!threadId) return;
  try {
    const res = await api.post(`messages/trash/${threadId}`, { user_id: user.value.id });
    if (res.data.success) {
      Swal.fire({ icon: 'success', title: 'Trashed', text: 'Conversation moved to trash.', timer: 1500, showConfirmButton: false });
      rightPaneMode.value = 'none';
      activeConversationId.value = null;
      activeConversation.value = null;
      fetchMessages();
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to trash conversation' });
  }
};

const restoreConversation = async (threadId) => {
  if (!threadId) return;
  try {
    const res = await api.post(`messages/restore/${threadId}`, { user_id: user.value.id });
    if (res.data.success) {
      Swal.fire({ icon: 'success', title: 'Restored', text: 'Conversation restored.', timer: 1500, showConfirmButton: false });
      rightPaneMode.value = 'none';
      activeConversationId.value = null;
      activeConversation.value = null;
      fetchMessages();
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to restore conversation' });
  }
};

const permanentlyDeleteConversation = async (threadId) => {
  if (!threadId) return;
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Yes, delete permanently!'
  });
  if (result.isConfirmed) {
    try {
      const res = await api.post('messages/permanently-delete', { 
        user_id: user.value.id, 
        message_ids: [threadId] 
      });
      if (res.data.success) {
        Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Conversation permanently deleted.', timer: 1500, showConfirmButton: false });
        rightPaneMode.value = 'none';
        activeConversationId.value = null;
        activeConversation.value = null;
        fetchMessages();
      }
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete permanently' });
    }
  }
};

watch(activeMsgTab, () => {
  rightPaneMode.value = 'none';
  activeConversationId.value = null;
  activeConversation.value = null;
  isSelectionMode.value = false;
  selectedThreads.value = [];
});

// ========================
// INQUIRIES STATE & LOGIC
// ========================
const inquiries = ref([]);
const loadingInquiries = ref(false);
const inquirySearchQuery = ref('');
const showReplyModal = ref(false);
const activeInquiry = ref(null);
const replyMessage = ref('');
const replying = ref(false);

const unreadInquiriesTotal = computed(() => {
  return inquiries.value.filter(i => i.status === 'new').length;
});

const repliedInquiriesCount = computed(() => {
  return inquiries.value.filter(i => (i.status || '').startsWith('replied')).length;
});

const filteredInquiries = computed(() => {
  if (!inquirySearchQuery.value) return inquiries.value;
  const q = inquirySearchQuery.value.toLowerCase();
  return inquiries.value.filter(i => 
    (i.name && i.name.toLowerCase().includes(q)) || 
    (i.email && i.email.toLowerCase().includes(q)) || 
    (i.subject && i.subject.toLowerCase().includes(q)) ||
    formatTicketNumber(i.id).toLowerCase().includes(q)
  );
});

const formatTicketNumber = (id) => {
  return 'INQ-' + String(id).padStart(5, '0');
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const fetchInquiries = async () => {
  if (!hasInquiriesFeature.value) return;
  loadingInquiries.value = true;
  try {
    const res = await api.get('/contact-inquiries');
    if (res.data && res.data.inquiries) {
      inquiries.value = res.data.inquiries;
    }
  } catch (error) {
    console.error('Error fetching inquiries:', error);
  } finally {
    loadingInquiries.value = false;
  }
};

const markAsRead = async (inquiry) => {
  try {
    const res = await api.post(`/contact-inquiries/${inquiry.id}/read`);
    if (res.status === 200 || res.data.message) {
      inquiry.status = 'read';
    }
  } catch (error) {
    console.error('Error marking as read:', error);
  }
};

const openReplyModal = (inquiry) => {
  activeInquiry.value = inquiry;
  replyMessage.value = '';
  showReplyModal.value = true;
};

const closeReplyModal = () => {
  showReplyModal.value = false;
  activeInquiry.value = null;
  replyMessage.value = '';
};

const submitReply = async () => {
  if (!activeInquiry.value || !replyMessage.value.trim()) return;
  
  replying.value = true;
  try {
    const res = await api.post(`/contact-inquiries/${activeInquiry.value.id}/reply`, {
      reply_message: replyMessage.value
    });
    
    if (res.status === 200 || res.data.message) {
      Swal.fire({
        icon: 'success',
        title: 'Reply Sent',
        text: 'Your reply was successfully emailed.',
        timer: 2000,
        showConfirmButton: false
      });
      activeInquiry.value.status = res.data.status || (userRole.value.includes('admin') ? 'replied_director' : 'replied_staff');
      closeReplyModal();
    }
  } catch (error) {
    console.error('Error sending reply:', error);
    Swal.fire({
      icon: 'error',
      title: 'Failed to Send',
      text: error?.messages?.error || error?.message || 'The email could not be sent. You might have reached your daily limit. Please try manually.'
    });
  } finally {
    replying.value = false;
  }
};

const deleteInquiry = async (inquiry) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "This inquiry will be permanently deleted from the database. This action cannot be undone.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  });

  if (result.isConfirmed) {
    try {
      const res = await api.delete(`/contact-inquiries/${inquiry.id}`);
      if (res.status === 200) {
        Swal.fire('Deleted!', 'The inquiry has been deleted.', 'success');
        inquiries.value = inquiries.value.filter(i => i.id !== inquiry.id);
      }
    } catch (error) {
      console.error('Error deleting inquiry:', error);
      Swal.fire('Error!', 'Failed to delete the inquiry.', 'error');
    }
  }
};

// ========================
// LIFECYCLE & POLLING
// ========================
let commPollInterval = null;

onMounted(() => {
  fetchUsers();
  fetchMyDocuments();
  fetchMessages();
  if (hasInquiriesFeature.value) {
    fetchInquiries();
  }
  
  commPollInterval = setInterval(() => {
    fetchMessages();
    if (hasInquiriesFeature.value) {
      fetchInquiries();
    }
  }, 15000);
});

onUnmounted(() => {
  if (commPollInterval) clearInterval(commPollInterval);
});
</script>

<style scoped>
.comm-main-viewport {
  padding: 0;
  width: 100%;
  min-height: calc(100vh - 5rem);
  display: flex;
  flex-direction: column;
}

.comm-container {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

/* Header & Tab Switcher Bar */
.comm-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.25rem;
  padding: 0.25rem 0;
}

.comm-title-area {
  flex-shrink: 0;
}

.comm-tab-switcher {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  background: rgba(18, 18, 38, 0.85);
  padding: 0.35rem;
  border-radius: 1rem;
  border: 1px solid rgba(147, 51, 234, 0.3);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(16px);
}

.tab-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.15rem;
  border-radius: 0.75rem;
  border: none;
  background: transparent;
  color: #94a3b8;
  font-size: 0.875rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
}

.tab-btn:hover:not(.active) {
  color: #f8fafc;
  background: rgba(255, 255, 255, 0.06);
}

.tab-btn.active {
  background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
  color: #ffffff;
  box-shadow: 0 4px 15px rgba(147, 51, 234, 0.4);
  transform: translateY(-1px);
}

.tab-badge {
  background: #ef4444;
  color: #ffffff;
  font-size: 0.65rem;
  font-weight: 800;
  padding: 0.15rem 0.45rem;
  border-radius: 9999px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}

/* Vue Tab Transition */
.comm-tab-fade-enter-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.comm-tab-fade-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 1, 1);
}
.comm-tab-fade-enter-from {
  opacity: 0;
  transform: translateY(12px) scale(0.99);
  filter: blur(4px);
}
.comm-tab-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.99);
  filter: blur(4px);
}

.comm-tab-panel {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  width: 100%;
}

/* TWG Note */
.info-note-twg {
  margin-bottom: 1rem;
  padding: 0.85rem 1.25rem;
  background-color: #1e293b;
  border: 1px solid rgba(147, 51, 234, 0.3);
  border-left: 4px solid #9333ea;
  border-radius: 0.75rem;
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
}

.info-note-icon {
  background: rgba(147, 51, 234, 0.2);
  padding: 0.4rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c084fc;
}

.info-note-twg h4 {
  margin: 0 0 0.15rem 0;
  color: #f8fafc;
  font-size: 0.9rem;
  font-weight: 700;
}

.info-note-twg p {
  margin: 0;
  color: #cbd5e1;
  font-size: 0.825rem;
  line-height: 1.4;
}

/* Messenger Container */
.messenger-container {
  flex-grow: 1;
  display: flex;
  background: linear-gradient(135deg, #16162a 0%, #121226 100%);
  border: 1px solid rgba(147, 51, 234, 0.25);
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.5);
  min-height: 680px;
  height: calc(100vh - 13rem);
}

.messenger-sidebar {
  width: 340px;
  background: rgba(0, 0, 0, 0.25);
  border-right: 1px solid rgba(147, 51, 234, 0.2);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.sidebar-top {
  padding: 1rem;
  border-bottom: 1px solid rgba(147, 51, 234, 0.2);
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.85rem;
}

.sidebar-header h2 {
  color: #f8fafc;
  font-size: 1.15rem;
  margin: 0;
  font-weight: 800;
}

.sidebar-actions {
  display: flex;
  gap: 0.4rem;
}

.icon-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.icon-btn:hover {
  background: rgba(147, 51, 234, 0.15);
  color: #f8fafc;
}

.primary-icon-btn {
  background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
  color: white;
}

.primary-icon-btn:hover {
  box-shadow: 0 4px 12px rgba(147, 51, 234, 0.5);
  transform: translateY(-1px);
}

.danger-icon-btn:hover { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
.success-icon-btn:hover { background: rgba(34, 197, 94, 0.2); color: #86efac; }
.active-selection { background: rgba(147, 51, 234, 0.25) !important; color: #c084fc !important; }

.sidebar-tabs {
  display: flex;
  background: rgba(0, 0, 0, 0.4);
  border-radius: 0.6rem;
  padding: 0.2rem;
  border: 1px solid rgba(147, 51, 234, 0.15);
}

.sidebar-tabs button {
  flex: 1;
  padding: 0.45rem;
  border: none;
  background: transparent;
  color: #94a3b8;
  border-radius: 0.45rem;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.8rem;
  transition: all 0.2s;
}

.sidebar-tabs button.active {
  background: rgba(147, 51, 234, 0.3);
  color: #c084fc;
  border: 1px solid rgba(147, 51, 234, 0.4);
}

.conversation-list {
  flex-grow: 1;
  overflow-y: auto;
  padding: 0.5rem;
}

.conversation-item {
  display: flex;
  gap: 0.75rem;
  padding: 0.75rem;
  border-radius: 0.75rem;
  cursor: pointer;
  transition: background 0.2s;
  margin-bottom: 0.25rem;
  border: 1px solid transparent;
}

.conversation-item:hover {
  background: rgba(147, 51, 234, 0.08);
  border-color: rgba(147, 51, 234, 0.15);
}

.conversation-item.active {
  background: rgba(147, 51, 234, 0.2);
  border-color: rgba(147, 51, 234, 0.4);
}

.conversation-item.selected {
  background: rgba(147, 51, 234, 0.2);
  border-color: rgba(147, 51, 234, 0.4);
}

.conv-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(147, 51, 234, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c084fc;
  position: relative;
  flex-shrink: 0;
}

.unread-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 11px;
  height: 11px;
  background: #3b82f6;
  border-radius: 50%;
  border: 2px solid #16162a;
}

.conv-details {
  flex-grow: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.conv-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 0.15rem;
}

.conv-name {
  color: #f8fafc;
  font-weight: 600;
  font-size: 0.95rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conv-name.unread {
  color: #93c5fd;
  font-weight: 800;
}

.conv-time {
  color: #64748b;
  font-size: 0.7rem;
  white-space: nowrap;
}

.conv-role {
  color: #94a3b8;
  font-size: 0.75rem;
  margin-bottom: 0.15rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conv-preview {
  color: #94a3b8;
  font-size: 0.8rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conv-preview.unread {
  color: #cbd5e1;
  font-weight: 600;
}

.conv-checkbox {
  display: flex;
  align-items: center;
  margin-right: 0.35rem;
}

.conv-checkbox input {
  width: 16px;
  height: 16px;
  accent-color: #9333ea;
  cursor: pointer;
}

.bulk-action-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.6rem 0.85rem;
  background: rgba(0, 0, 0, 0.5);
  border-top: 1px solid rgba(147, 51, 234, 0.2);
}

.selected-count {
  color: #f8fafc;
  font-size: 0.8rem;
  font-weight: 600;
}

.bulk-actions {
  display: flex;
  gap: 0.4rem;
}

.bulk-btn {
  border: none;
  padding: 0.35rem 0.65rem;
  border-radius: 0.4rem;
  cursor: pointer;
  font-size: 0.75rem;
  font-weight: 700;
  color: white;
  transition: opacity 0.2s;
}

.bulk-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.bulk-btn.trash-btn { background: #eab308; }
.bulk-btn.restore-btn { background: #3b82f6; }
.bulk-btn.delete-btn { background: #ef4444; }

/* Right Chat Pane */
.messenger-chat-pane {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  background: transparent;
  position: relative;
  min-width: 0;
}

.no-chat-selected {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #64748b;
  text-align: center;
  padding: 2rem;
}

.no-chat-icon {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(147, 51, 234, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.25rem;
}

.no-chat-icon span {
  font-size: 2.25rem;
  color: rgba(147, 51, 234, 0.5);
}

.active-chat-wrapper, .compose-pane {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.chat-header {
  padding: 0.85rem 1.25rem;
  border-bottom: 1px solid rgba(147, 51, 234, 0.2);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba(0, 0, 0, 0.25);
}

.chat-header-info h3 {
  color: #f8fafc;
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
}

.chat-role {
  color: #94a3b8;
  font-size: 0.75rem;
}

.chat-header-actions {
  display: flex;
  gap: 0.4rem;
}

.mobile-back-btn {
  display: none;
  background: transparent;
  border: none;
  color: #f8fafc;
  padding: 0.25rem;
  margin-right: 0.4rem;
  cursor: pointer;
  border-radius: 50%;
  align-items: center;
  justify-content: center;
}

.mobile-back-btn:hover { background: rgba(255, 255, 255, 0.1); }

@media (max-width: 768px) {
  .mobile-back-btn { display: flex; }
  .hidden-on-mobile { display: none !important; }
  .messenger-sidebar { width: 100%; border-right: none; }
}

.chat-history {
  flex-grow: 1;
  padding: 1.25rem;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.chat-bubbles-container {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.chat-message-row {
  display: flex;
  width: 100%;
}

.chat-message-row.sent { justify-content: flex-end; }
.chat-message-row.received { justify-content: flex-start; }

.chat-bubble {
  max-width: 82%;
  padding: 0.75rem 1rem;
  border-radius: 1.15rem;
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.chat-message-row.received .chat-bubble {
  background: rgba(22, 22, 42, 0.95);
  color: #f8fafc;
  border-bottom-left-radius: 0.25rem;
  border: 1px solid rgba(147, 51, 234, 0.25);
}

.chat-message-row.sent .chat-bubble {
  background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
  color: white;
  border-bottom-right-radius: 0.25rem;
}

.msg-text {
  font-size: 0.95rem;
  line-height: 1.5;
  white-space: pre-wrap;
}

.msg-time {
  font-size: 0.68rem;
  opacity: 0.75;
  align-self: flex-end;
  margin-top: 0.2rem;
  font-family: monospace;
}

.msg-attachments {
  background: rgba(0, 0, 0, 0.2);
  padding: 0.5rem;
  border-radius: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.attachment-label {
  font-size: 0.75rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  opacity: 0.9;
}

.attachment-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.attachment-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  padding: 0.3rem 0.6rem;
  border-radius: 0.4rem;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.attachment-btn:hover {
  background: rgba(255, 255, 255, 0.35);
}

/* Chat Composer */
.chat-composer {
  padding: 0.85rem 1.25rem;
  background: rgba(0, 0, 0, 0.3);
  border-top: 1px solid rgba(147, 51, 234, 0.2);
  position: relative;
}

.composer-input-area {
  display: flex;
  align-items: flex-end;
  gap: 0.6rem;
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(147, 51, 234, 0.3);
  padding: 0.45rem 0.65rem;
  border-radius: 1.25rem;
  transition: all 0.2s;
}

.composer-input-area:focus-within {
  border-color: #c084fc;
  box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.3);
}

.composer-textarea {
  flex-grow: 1;
  background: transparent;
  border: none;
  color: #f8fafc;
  font-size: 0.95rem;
  font-family: inherit;
  resize: none;
  padding: 0.35rem 0;
  outline: none;
  max-height: 110px;
  line-height: 1.4;
}

.composer-textarea::placeholder { color: #64748b; }

.attach-btn { color: #94a3b8; }
.attach-btn:hover { color: #c084fc; background: rgba(147, 51, 234, 0.15); }

.send-btn {
  background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
  border: none;
  color: white;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.send-btn:hover:not(:disabled) {
  transform: scale(1.05);
  box-shadow: 0 4px 10px rgba(147, 51, 234, 0.5);
}

.send-btn:disabled {
  background: rgba(0, 0, 0, 0.3);
  color: #64748b;
  cursor: not-allowed;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.attachment-menu {
  position: absolute;
  bottom: 100%;
  left: 1.25rem;
  background: #16162a;
  border: 1px solid rgba(147, 51, 234, 0.4);
  border-radius: 0.85rem;
  padding: 0.85rem;
  width: 300px;
  box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.6);
  margin-bottom: 0.5rem;
  z-index: 20;
}

.attachment-menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.6rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  padding-bottom: 0.4rem;
}

.attachment-menu-header h4 {
  margin: 0;
  color: #f8fafc;
  font-size: 0.85rem;
  font-weight: 700;
}

.attachment-menu-header button {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
}

.attachment-menu-list {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  max-height: 180px;
  overflow-y: auto;
}

.doc-checkbox {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  color: #cbd5e1;
  font-size: 0.8rem;
  cursor: pointer;
}

.doc-checkbox input { accent-color: #9333ea; }

.composer-attachments-preview {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  margin-bottom: 0.6rem;
}

.doc-chip {
  background: rgba(147, 51, 234, 0.25);
  color: #e9d5ff;
  padding: 0.2rem 0.5rem;
  border-radius: 0.4rem;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  border: 1px solid rgba(147, 51, 234, 0.35);
}

.doc-chip span {
  font-size: 0.9rem;
  cursor: pointer;
}

.compose-body {
  flex-grow: 1;
  padding: 1.25rem;
  overflow-y: auto;
}

.form-group { margin-bottom: 1.15rem; }
.form-label { display: block; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; font-size: 0.875rem; }
.sub-label { display: block; color: #cbd5e1; font-size: 0.8rem; margin-bottom: 0.25rem; }
.filter-group { margin-bottom: 0.85rem; }

.role-buttons, .office-buttons { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.role-btn, .office-btn {
  background: rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(147, 51, 234, 0.25);
  color: #cbd5e1;
  padding: 0.4rem 0.85rem;
  border-radius: 0.6rem;
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 600;
  transition: all 0.2s;
}

.role-btn.active, .office-btn.active {
  background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
  color: white;
  border-color: transparent;
  box-shadow: 0 2px 8px rgba(147, 51, 234, 0.4);
}

.user-list {
  border: 1px solid rgba(147, 51, 234, 0.2);
  border-radius: 0.6rem;
  max-height: 180px;
  overflow-y: auto;
  padding: 0.4rem;
  background: rgba(0, 0, 0, 0.3);
}

.user-item {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  color: #f8fafc;
  padding: 0.35rem 0.25rem;
  cursor: pointer;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  font-size: 0.85rem;
}

.user-item:last-child { border-bottom: none; }
.user-item input { accent-color: #9333ea; width: 15px; height: 15px; }
.user-email { color: #94a3b8; font-size: 0.75rem; }
.no-users-msg { color: #94a3b8; font-size: 0.85rem; padding: 0.5rem; text-align: center; }

.selected-user-chip {
  background: rgba(147, 51, 234, 0.25);
  color: #e9d5ff;
  padding: 0.25rem 0.65rem;
  border-radius: 9999px;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  border: 1px solid rgba(147, 51, 234, 0.4);
}

.checkbox-list {
  max-height: 200px;
  overflow-y: auto;
  padding: 0.5rem;
  border: 1px solid rgba(147, 51, 234, 0.2);
  border-radius: 0.6rem;
  background: rgba(0, 0, 0, 0.3);
}

.doc-item-row {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  padding: 0.45rem 0;
  cursor: pointer;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.doc-item-row:last-child { border-bottom: none; }

.form-input, .form-textarea, select.form-control {
  width: 100%;
  padding: 0.65rem 0.85rem;
  border-radius: 0.6rem;
  background: rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(147, 51, 234, 0.3);
  color: white;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s;
}

.form-input:focus, .form-textarea:focus, select.form-control:focus {
  border-color: #c084fc;
  box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.25);
}

select.form-control option { background: #16162a; color: #f8fafc; }
.form-textarea { resize: vertical; line-height: 1.4; }

.panel-footer {
  margin-top: 1.25rem;
  padding-top: 0.85rem;
  border-top: 1px solid rgba(147, 51, 234, 0.2);
}

.btn-primary {
  background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
  color: white;
  border: none;
  padding: 0.65rem 1.25rem;
  border-radius: 0.6rem;
  cursor: pointer;
  font-weight: 700;
  box-shadow: 0 4px 10px rgba(147, 51, 234, 0.3);
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 15px rgba(147, 51, 234, 0.45);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.08);
  color: #e2e8f0;
  padding: 0.55rem 1rem;
  border-radius: 0.6rem;
  font-weight: 600;
  border: 1px solid rgba(255, 255, 255, 0.12);
  transition: all 0.2s;
  cursor: pointer;
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.14);
}

.spinner {
  border: 3px solid rgba(255, 255, 255, 0.1);
  border-top-color: #c084fc;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.loading-state {
  display: flex;
  justify-content: center;
  padding: 2rem;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #64748b;
  text-align: center;
  padding: 2rem;
}

.empty-state .material-symbols-outlined {
  font-size: 2.75rem;
  color: rgba(148, 163, 184, 0.3);
  margin-bottom: 0.75rem;
}

.announcement-banner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.85rem;
  background: rgba(147, 51, 234, 0.1);
  color: #cbd5e1;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  font-style: italic;
  font-size: 0.85rem;
}

.announcement-banner .material-symbols-outlined {
  font-size: 1.15rem;
  color: #c084fc;
}

/* Glass Card */
.glass-card {
  background: linear-gradient(135deg, rgba(22, 22, 42, 0.85) 0%, rgba(18, 18, 38, 0.9) 100%);
  backdrop-filter: blur(16px);
  border-radius: 1.25rem;
  border: 1px solid rgba(147, 51, 234, 0.25);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
}

/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(6px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

/* Custom Scrollbars */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(147, 51, 234, 0.4);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(147, 51, 234, 0.6);
}
</style>
