<?php
namespace Files;

class File
{
    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new Exception("Error Processing Request", 1);
        }
        $this->path = realpath($path);
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
            throw new \Exception("Source don't exist : " . $this->path, 1);
        }

        // La destination ne doit pas exister.
        if (file_exists($dest)) {
            throw new \Exception("Destination already exist : $dest", 1);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->path,
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $path => $file) {
            if ($file->isDir()) {
                mkdir($dest . $iterator->getSubPathName(), 0777, true);
                continue;
            }
            copy($path, $dest . $iterator->getSubPathName());
        }
    }

    /**
     * Delete file or folder
     */
    public function delete()
    {
        if (!file_exists($this->path)) {
            throw new \Exception(
                "Can't delete something unexisting : " . $this->path,
                1
            );
        }

        if (is_dir($this->path)){
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $this->path,
                    \RecursiveDirectoryIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                    continue;
                }
                unlink($file->getPathname());
            }
            rmdir($this->path);
            return;
        }

        unlink($this->path);
    }

    /**
     * Move file or folder
     * @param  string $destination path where to copy files
     */
    public function move($dest)
    {
        $this->copy($dest);
        $this->delete();
    }

    /**
     * Recursively evaluate file/directory disk usage
     * @return integer size in Bytes
     */
    public function diskUsage()
    {
        if (is_dir($this->path)) {
            $diskUsage = 0;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $this->path,
                    \RecursiveDirectoryIterator::SKIP_DOTS
                )
            );

            foreach ($iterator as $file) {
                $diskUsage += $file->getSize();
            }
            return $diskUsage;
        }

        return filesize($this->path);
    }
}
