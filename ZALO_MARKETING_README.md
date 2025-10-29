# 📱 Hệ thống Quản lý Chiến dịch Marketing Zalo

## Tổng quan

Hệ thống quản lý chiến dịch marketing trên Zalo với các tính năng:
- ✅ Quản lý tài khoản Zalo (email/SĐT, mật khẩu, token)
- ✅ Quản lý nhóm mục tiêu (nhóm đối thủ và nhóm của mình)
- ✅ Theo dõi gửi tin nhắn hàng loạt
- ✅ Theo dõi tỷ lệ chuyển đổi (conversion rate)
- ✅ Báo cáo chi tiết và dashboard thống kê

## Cài đặt

### 1. Chạy Migration

```bash
php artisan migrate
```

Migration sẽ tạo các bảng:
- `zalo_accounts` - Quản lý tài khoản Zalo
- `target_groups` - Quản lý nhóm mục tiêu
- `group_members` - Thành viên trong nhóm
- `message_campaigns` - Chiến dịch gửi tin
- `message_logs` - Lịch sử gửi tin
- `conversion_logs` - Theo dõi conversion

### 2. Truy cập hệ thống

Dashboard Zalo Marketing: `http://yourdomain.com/admin/zalo`

## Hướng dẫn sử dụng

### 1. Quản lý Tài khoản Zalo

**Đường dẫn:** `/admin/zalo/accounts`

**Các bước:**
1. Nhấn "Thêm tài khoản"
2. Nhập thông tin:
   - Tên tài khoản (dễ nhớ)
   - Email hoặc SĐT đăng nhập Zalo
   - Mật khẩu (tùy chọn, sẽ được mã hóa)
   - Access Token (nếu có từ Zalo Developer)
   - Giới hạn tin nhắn/ngày (khuyến nghị: 50-100)
   - Trạng thái: Active/Inactive/Blocked/Error

**Lưu ý:**
- Mật khẩu và token được mã hóa bằng Laravel Crypt
- Hệ thống tự động reset bộ đếm tin nhắn mỗi ngày
- Không nên gửi quá 100 tin/ngày/tài khoản để tránh bị spam

### 2. Quản lý Nhóm Mục tiêu

**Đường dẫn:** `/admin/zalo/groups`

**Loại nhóm:**

#### a) Nhóm đối thủ (Competitor)
- Nhóm cần quét thành viên để gửi tin nhắn
- Nhập link nhóm Zalo
- Sử dụng tool bên ngoài để quét thành viên và import vào `group_members`

#### b) Nhóm của mình (Own)
- Nhóm để kéo thành viên về
- Dùng để theo dõi conversion rate

**Các bước:**
1. Nhấn "Thêm nhóm"
2. Nhập thông tin:
   - Tên nhóm
   - Link nhóm Zalo
   - Loại: Đối thủ hoặc Của mình
   - Chủ đề (tùy chọn)
   - Số thành viên (tự động cập nhật khi quét)
   - Ngày khai giảng (nếu có)

### 3. Tạo Chiến dịch Marketing

**Đường dẫn:** `/admin/zalo/campaigns`

**Các bước:**
1. Nhấn "Tạo chiến dịch"
2. Chọn nhóm mục tiêu (nhóm đối thủ)
3. Chọn nhóm của mình (để theo dõi conversion)
4. Viết mẫu tin nhắn:
   ```
   Chào {name}, mình thấy bạn trong nhóm {group_name}.
   Mình có nhóm học tiếng Anh miễn phí, bạn có muốn tham gia không?
   Link: [link nhóm của bạn]
   ```
5. Thiết lập:
   - Ngày bắt đầu/kết thúc
   - Mục tiêu gửi tin/ngày
   - Trạng thái: Draft → Active

**Biến có thể dùng:**
- `{name}` - Tên thành viên
- `{group_name}` - Tên nhóm

### 4. Theo dõi Conversion

Hệ thống theo dõi:
- **Tin nhắn đã gửi**: Tổng số tin đã gửi
- **Tin nhắn thành công**: Số tin gửi thành công
- **Số người join nhóm**: Từ `conversion_logs`
- **Tỷ lệ chuyển đổi**: (Số người join / Tin gửi thành công) × 100%
- **Thời gian chuyển đổi**: Số ngày từ lúc gửi tin đến khi join

**Cách ghi nhận conversion:**

```php
use App\Models\ConversionLog;
use App\Models\GroupMember;

// Khi phát hiện có người join nhóm của mình
ConversionLog::create([
    'campaign_id' => $campaignId,
    'group_member_id' => $memberId,
    'message_log_id' => $messageLogId, // ID tin nhắn đã gửi
    'own_group_id' => $ownGroupId,
    'joined_at' => now(),
    'notes' => 'Join từ chiến dịch X'
]);

// Cập nhật trạng thái member
$member = GroupMember::find($memberId);
$member->markAsConverted();

// Cập nhật thống kê chiến dịch
$campaign->updateStatistics();
```

### 5. Xem Báo cáo

**Dashboard:** `/admin/zalo`
- Tổng quan hệ thống
- Biểu đồ tin nhắn theo ngày
- Biểu đồ conversion theo ngày
- Top chiến dịch hiệu quả nhất
- Hiệu suất từng tài khoản

**Báo cáo chi tiết chiến dịch:** `/admin/zalo/campaigns/{id}/report`
- Thống kê tổng quan
- Biểu đồ theo ngày
- Thời gian chuyển đổi trung bình
- Chi tiết từng ngày

## Quy trình làm việc

### Quy trình chuẩn:

1. **Chuẩn bị:**
   - Thêm tài khoản Zalo
   - Thêm nhóm đối thủ
   - Thêm nhóm của mình
   - Quét thành viên nhóm đối thủ (dùng tool bên ngoài)

2. **Tạo chiến dịch:**
   - Tạo chiến dịch với trạng thái "Draft"
   - Kiểm tra mẫu tin nhắn
   - Chuyển sang "Active" khi sẵn sàng

3. **Gửi tin nhắn:** (Cần tích hợp với Zalo API)
   ```php
   use App\Models\MessageLog;
   use App\Models\ZaloAccount;
   use App\Models\GroupMember;

   // Lấy tài khoản có thể gửi tin
   $account = ZaloAccount::where('status', 'active')
       ->whereRaw('messages_sent_today < daily_message_limit')
       ->first();

   if ($account && $account->canSendMessage()) {
       // Lấy member chưa được liên hệ
       $member = GroupMember::where('target_group_id', $targetGroupId)
           ->where('status', 'new')
           ->first();

       // Tạo log
       $log = MessageLog::create([
           'campaign_id' => $campaign->id,
           'zalo_account_id' => $account->id,
           'group_member_id' => $member->id,
           'message_content' => str_replace(
               ['{name}', '{group_name}'],
               [$member->display_name, $targetGroup->group_name],
               $campaign->message_template
           ),
           'status' => 'pending'
       ]);

       // Gửi tin qua Zalo API (cần implement)
       // $result = ZaloAPI::sendMessage(...);

       // Cập nhật log
       $log->markAsDelivered(); // hoặc markAsFailed($error)
       
       // Cập nhật account counter
       $account->incrementMessageCount();
       
       // Cập nhật member status
       $member->markAsContacted();
   }
   ```

4. **Theo dõi conversion:** (Manual hoặc tự động)
   - Kiểm tra xem member đã join nhóm của mình chưa
   - Tạo conversion log nếu có

5. **Xem báo cáo:**
   - Kiểm tra conversion rate
   - Tối ưu mẫu tin nhắn
   - Điều chỉnh chiến dịch

## API Endpoints

### Zalo Accounts
- `GET /admin/zalo/accounts` - Danh sách tài khoản
- `POST /admin/zalo/accounts` - Tạo tài khoản
- `PUT /admin/zalo/accounts/{id}` - Cập nhật
- `DELETE /admin/zalo/accounts/{id}` - Xóa
- `POST /admin/zalo/accounts/{id}/reset-counter` - Reset bộ đếm

### Target Groups
- `GET /admin/zalo/groups` - Danh sách nhóm
- `POST /admin/zalo/groups` - Tạo nhóm
- `PUT /admin/zalo/groups/{id}` - Cập nhật
- `DELETE /admin/zalo/groups/{id}` - Xóa
- `GET /admin/zalo/groups/{id}/members` - Danh sách thành viên

### Campaigns
- `GET /admin/zalo/campaigns` - Danh sách chiến dịch
- `POST /admin/zalo/campaigns` - Tạo chiến dịch
- `PUT /admin/zalo/campaigns/{id}` - Cập nhật
- `DELETE /admin/zalo/campaigns/{id}` - Xóa
- `GET /admin/zalo/campaigns/{id}/report` - Báo cáo chi tiết
- `POST /admin/zalo/campaigns/{id}/update-stats` - Cập nhật thống kê

### Dashboard
- `GET /admin/zalo` - Dashboard chính
- `GET /admin/zalo/conversion-funnel` - API conversion funnel

## Models & Relationships

### ZaloAccount
```php
// Relationships
- hasMany(MessageLog) - messageLogs
- hasMany(MessageLog) - todayMessageLogs

// Methods
- canSendMessage(): bool
- incrementMessageCount(): void
- getRemainingMessagesAttribute(): int
```

### TargetGroup
```php
// Relationships
- hasMany(GroupMember) - members
- hasMany(GroupMember) - newMembers
- hasMany(GroupMember) - contactedMembers
- hasMany(GroupMember) - convertedMembers
- hasMany(MessageCampaign) - campaigns
- hasMany(ConversionLog) - conversions

// Methods
- updateMembersCount(): void
- isOwnGroup(): bool
- isCompetitorGroup(): bool
```

### MessageCampaign
```php
// Relationships
- belongsTo(TargetGroup) - targetGroup
- belongsTo(TargetGroup) - ownGroup
- hasMany(MessageLog) - messageLogs
- hasMany(ConversionLog) - conversions

// Methods
- updateConversionRate(): void
- updateStatistics(): void
- isActive(): bool
- isDailyTargetReached(): bool
```

### GroupMember
```php
// Relationships
- belongsTo(TargetGroup) - targetGroup
- hasMany(MessageLog) - messageLogs
- hasOne(ConversionLog) - conversionLog

// Methods
- hasBeenContacted(): bool
- hasConverted(): bool
- markAsContacted(): void
- markAsConverted(): void
```

## Tips & Best Practices

### 1. Tránh Spam
- Giới hạn 50-100 tin/ngày/tài khoản
- Không gửi cùng nội dung cho nhiều người
- Thêm khoảng delay giữa các tin nhắn
- Sử dụng nhiều tài khoản để phân tải

### 2. Tăng Conversion Rate
- Personalize tin nhắn (dùng tên, nhóm)
- Tin nhắn ngắn gọn, rõ ràng
- Có call-to-action rõ ràng
- Gửi vào khung giờ phù hợp (19h-21h)
- Test nhiều mẫu tin khác nhau

### 3. Quản lý Hiệu quả
- Theo dõi conversion rate hàng ngày
- A/B testing các mẫu tin
- Phân tích thời gian chuyển đổi
- Tối ưu mục tiêu gửi tin/ngày
- Theo dõi tài khoản bị block

### 4. Bảo mật
- Mật khẩu và token được mã hóa
- Chỉ admin mới truy cập được
- Backup dữ liệu thường xuyên
- Không share thông tin tài khoản

## Mở rộng

### Tích hợp Zalo API
Cần implement:
- Zalo Official Account API
- Zalo Mini App API (nếu có)
- Authentication và Token Management
- Send Message API
- Get Group Members API

### Automation
- Cron job gửi tin tự động
- Auto detect conversion (webhook từ nhóm)
- Auto retry failed messages
- Daily report email

### Analytics
- Funnel analysis
- A/B testing framework
- ROI calculation
- Predictive analytics

## Troubleshooting

### Lỗi thường gặp:

1. **Không gửi được tin:**
   - Kiểm tra token hết hạn
   - Kiểm tra account status
   - Kiểm tra daily limit

2. **Conversion rate = 0:**
   - Kiểm tra có tạo conversion log chưa
   - Chạy `$campaign->updateStatistics()`

3. **Members không hiển thị:**
   - Kiểm tra đã import members chưa
   - Kiểm tra relationship trong Model

## Support

Liên hệ: [Your contact info]

---

**Version:** 1.0.0  
**Last Updated:** October 22, 2025

