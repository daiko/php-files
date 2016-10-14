<?php
namespace Files;

class File
{
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function __toString()
    {
        return $this->path;
    }

    /**
     * Copy this file to destination
     * @param  [type] $destination [description]
     * @return [type]          [description]
     */
    public function copy($dest)
    {
        // La source n'existe pas
        if (!file_exists($this->path)) {
            throw new \Exception("Source don't exist : $src", 1);
        }

        // En cas de copie d'un fichier dans un repertoire.
        if (is_dir($dest) and !is_dir($this->path)) {
            $dest .= basename($this->path);
        }

        // La destination ne doit pas exister.
        if (file_exists($dest)) {
            throw new \Exception("Destination already exist : $dest", 1);
        }

        $this->internalCopy($this->path, $dest);
    }

    /**
     * Delete file or folder
     * @return [type] [description]
     */
    public function delete()
    {
        if (!file_exists($this->path)) {
            throw new \Exception("Can't delete something unexisting : $src", 1);
        }

        $this->internalDelete($this->path);
    }

    /**
     * Move file or folder
     * @param  [type] $destination [description]
     * @return [type]              [description]
     */
    public function move($dest)
    {
        $this->copy($dest);
        $this->delete();
    }

    /**
     * [internalCopy description]
     * @param  string $src  [description]
     * @param  string $dest [description]
     * @return [type]       [description]
     */
    private function internalCopy($src, $dest)
    {
        if (is_file($src)) {
            if (!copy($src, $dest)) {
                throw new \Exception(
                    "Copying error : " . $src . " -> "  . $dest,
                    1
                );
            }
            return;
        }

        if (is_dir($src)) {
            if (!mkdir($dest)) {
                throw new \Exception(
                    "Can't create destination folder : $dest",
                    1
                );
            }

            $file2ignore = array('.', '..');
            if ($res = opendir($src)) {
                while (($file = readdir($res)) !== false) {
                    if (!in_array($file, $file2ignore)) {
                        $this->internalCopy(
                            $src . '/' . $file,
                            $dest . '/' . $file
                        );
                    }
                }
                closedir($res);
            }
            return;
        }

        if (is_link($src)) {
            // TODO
            throw new \Exception("Can't copy link (for now)", 1);
            return;
        }

        throw new \Exception("$src is not file, link or folder", 1);
    }

    private function internalDelete($path)
    {
        if (is_file($path) or is_link($path)) {
            if (!unlink($path)) {
                throw new \Exception("Can't delete : $path", 1);
            }
            return;
        }

        if (is_dir($path)) {
            $file2ignore = array('.', '..');
            if ($res = opendir($path)) {
                while (($file = readdir($res)) !== false) {
                    if (!in_array($file, $file2ignore)) {
                        $this->internalDelete($path . '/' . $file);
                    }
                }
                closedir($res);
            }

            if (!rmdir($path)) {
                throw new \Exception("Error deleting : $path", 1);
            }

            return;
        }

        throw new \Exception("$path is not file, link or folder", 1);
    }
}
