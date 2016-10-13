<?php
namespace PhpFiles;

class File
{
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Copy this file to destination
     * @param  [type] $destination [description]
     * @return [type]          [description]
     */
    public function copy($destination)
    {
        // Erreur si la destination existe déjà.
        if (is_file($destination)
            or is_dir($destination)
            or is_link($destination)) {
            throw new Exception(
                "Destination : " . $destination .  "already exist",
                1
            );
        }

        if (is_file($this->path)) {
            if (!copy($this->path, $destination)) {
                throw new Exception(
                    "Copying error : " . $this->path . " -> "  . $destination,
                    1
                );
            }
        }

        if (is_dir($this->path)) {
            if (!mkdir($destination)) {
                throw new Exception(
                    "Can't create destination folder : $destination",
                    1
                );
            }
            $this->copyFolder($this->path, $destination);
        }

        if (is_link($this->path)) {
            throw new Exception("Can't copy link (for now)", 1);
        }

        throw new Exception("This is impossible.", 1);
    }

    public function delete()
    {
        if (is_file($this->path)) {
            if (!unlink($this->path)) {
                throw new Exception("Can't delete : " . $this->path, 1);
            }
        }
        if (is_dir($path)) {
            $this->deleteFolder($path);
        }
    }

    public function move($destination)
    {
        $this->copy($destination);
        $this->delete();
    }

    private function deleteFolder($path)
    {
        $file2ignore = array('.', '..');
        if ($res = opendir($path)) {
            while (($file = readdir($res)) !== false) {
                if (!in_array($file, $file2ignore)) {
                    $this->delete($path . '/' . $file);
                }
            }
            closedir($res);
        }

        if (!rmdir($path)) {
            throw new Exception("Error deleting : $path", 1);
        };
    }

    private function copyFolder($srcPath, $destination)
    {
        $file2ignore = array('.', '..');
        if ($res = opendir($srcPath)) {
            while (($file = readdir($res)) !== false) {
                if (!in_array($file, $file2ignore)) {
                    $this->copy($srcPath . '/' . $file, $destination . '/' . $file);
                }
            }
            closedir($res);
        }
    }
}
