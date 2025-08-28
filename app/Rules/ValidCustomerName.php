<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCustomerName implements Rule
{
    private $errorMessage = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            $this->errorMessage = 'Tên khách hàng phải là chuỗi ký tự.';
            return false;
        }

        $name = trim($value);

        // Kiểm tra độ dài
        if (strlen($name) < 2) {
            $this->errorMessage = 'Tên khách hàng phải có ít nhất 2 ký tự.';
            return false;
        }

        if (strlen($name) > 100) {
            $this->errorMessage = 'Tên khách hàng không được vượt quá 100 ký tự.';
            return false;
        }

        // Kiểm tra ký tự hợp lệ
        if (!$this->hasValidCharacters($name)) {
            $this->errorMessage = 'Tên khách hàng chỉ được chứa chữ cái, dấu cách và các ký tự tiếng Việt.';
            return false;
        }

        // Kiểm tra không có nhiều dấu cách liên tiếp
        if (preg_match('/\s{2,}/', $name)) {
            $this->errorMessage = 'Tên khách hàng không được có nhiều dấu cách liên tiếp.';
            return false;
        }

        // Kiểm tra không bắt đầu hoặc kết thúc bằng dấu cách
        if ($name !== trim($name)) {
            $this->errorMessage = 'Tên khách hàng không được bắt đầu hoặc kết thúc bằng dấu cách.';
            return false;
        }

        // Kiểm tra không chứa toàn bộ số
        if (preg_match('/^\d+$/', $name)) {
            $this->errorMessage = 'Tên khách hàng không được chỉ chứa số.';
            return false;
        }

        // Kiểm tra định dạng viết hoa/viết thường bất thường
        if ($this->hasInconsistentCase($name)) {
            $this->errorMessage = 'Tên khách hàng có định dạng viết hoa/viết thường không phù hợp.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }

    /**
     * Kiểm tra ký tự hợp lệ
     */
    private function hasValidCharacters(string $name): bool
    {
        // Cho phép: chữ cái Latin, chữ cái tiếng Việt, dấu cách, dấu phẩy, dấu chấm, dấu gạch ngang
        $pattern = '/^[a-zA-ZàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ\s,.-]+$/u';

        return preg_match($pattern, $name);
    }

    /**
     * Kiểm tra định dạng viết hoa/viết thường bất thường
     */
    private function hasInconsistentCase(string $name): bool
    {
        // Chỉ kiểm tra pattern rất bất thường: chữ hoa + chữ thường + chữ hoa trong cùng một từ
        // Ví dụ: "TrầN", "TrọNg" - không phù hợp
        $words = explode(' ', $name);

        foreach ($words as $word) {
            if (strlen($word) > 3) {
                // Kiểm tra pattern: Chữ hoa + chữ thường + chữ hoa (bất thường)
                // Chỉ cảnh báo với pattern rất rõ ràng như TrầN, TrọNg
                if (preg_match('/^[A-ZÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ][a-zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]{1,2}[A-ZÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ]/', $word)) {
                    return true;
                }
            }
        }

        return false;
    }
}
